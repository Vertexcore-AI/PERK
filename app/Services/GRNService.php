<?php

namespace App\Services;

use App\Models\GRN;
use App\Models\GRNItem;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Bin;
use App\Models\VendorItemMapping;
use App\Models\InventoryStock;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GRNService
{
    protected $inventoryService;
    protected $batchService;

    public function __construct(InventoryService $inventoryService, BatchService $batchService)
    {
        $this->inventoryService = $inventoryService;
        $this->batchService = $batchService;
    }

    /**
     * Process a complete GRN transaction
     */
    public function processGRN($grnData)
    {
        return DB::transaction(function () use ($grnData) {
            \Log::info('GRNService: Starting processGRN', ['vendor_id' => $grnData['vendor_id'] ?? 'missing']);

            // Validate GRN data
            $this->validateGRN($grnData);
            \Log::info('GRNService: Validation passed');

            // Create GRN header
            $grn = GRN::create([
                'vendor_id' => $grnData['vendor_id'],
                'inv_no' => $grnData['inv_no'],
                'billing_date' => $grnData['billing_date'],
            ]);
            \Log::info('GRNService: GRN header created', ['grn_id' => $grn->grn_id]);

            $totalAmount = 0;

            // Process each GRN item
            foreach ($grnData['items'] as $itemData) {
                // Resolve vendor item mapping (with auto-creation)
                $item = $this->resolveVendorItemMapping(
                    $grnData['vendor_id'],
                    $itemData['vendor_item_code'],
                    $itemData // Pass full item data for auto-creation
                );

                // Use standard business logic: unit_cost = purchase cost, selling_price = retail price
                $sellingPrice = $itemData['selling_price'] ?? ($itemData['unit_cost'] * 1.3); // Default 30% markup

                // Calculate costs with standard business logic
                $costs = $this->calculateCosts(
                    $itemData['unit_cost'], // Purchase cost from vendor
                    $itemData['discount'] ?? 0, // Vendor discount %
                    $itemData['vat'] ?? 0,
                    $sellingPrice, // Our retail price
                    $itemData['quantity'] ?? 1
                );

                // Create batch with standard business logic
                $batch = $this->batchService->createBatchWithNewPricing(
                    $item->id,
                    $grnData['vendor_id'],
                    [
                        'unit_price' => $itemData['unit_cost'], // Purchase cost from vendor
                        'actual_cost' => $costs['actual_cost'], // Final cost per unit (after discount + VAT)
                        'selling_price' => $sellingPrice, // Our retail price
                        'discount' => $itemData['discount'] ?? 0, // Vendor discount %
                        'vat' => $itemData['vat'] ?? 0,
                        'expiry_date' => $itemData['expiry_date'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                    ],
                    $itemData['quantity'] ?? 1
                );

                // Create GRN item record with standard business logic
                $grnItem = GRNItem::create([
                    'grn_id' => $grn->grn_id,
                    'item_id' => $item->id,
                    'batch_id' => $batch->id,
                    'vendor_item_code' => $itemData['vendor_item_code'],
                    'quantity' => $itemData['quantity'] ?? 1,
                    'unit_cost' => $itemData['unit_cost'], // Purchase cost from vendor
                    'selling_price' => $sellingPrice, // Our retail price
                    'discount' => $itemData['discount'] ?? 0, // Vendor discount %
                    'profit_margin' => $costs['profit_margin_amount'], // Profit amount per unit
                    'vat' => $itemData['vat'] ?? 0,
                    'total_cost' => $costs['total_cost'], // Total cost for quantity
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Resolve bin location if bin code provided
                $storeId = $itemData['store_id'] ?? \App\Models\Store::first()?->id;
                $binId = null;

                if (!empty($itemData['bin_code']) && $storeId) {
                    $binId = $this->resolveBinLocation($storeId, $itemData['bin_code']);
                } else {
                    $binId = $itemData['bin_id'] ?? null; // Fallback to direct bin_id if provided
                }

                // Update inventory stock (items are automatically available after GRN creation)
                if ($storeId) {
                    $this->inventoryService->updateStock(
                        $item->id,
                        $storeId,
                        $binId,
                        $batch->id,
                        $itemData['quantity'] ?? 1 // Use the actual quantity for inventory tracking
                    );
                }

                $totalAmount += $costs['total_cost'];
            }

            // Update GRN total
            $grn->total_amount = $totalAmount;
            $grn->save();

            return $grn->load('grnItems.item', 'vendor');
        });
    }

    /**
     * Resolve vendor item code to internal item
     * Auto-creates items if they don't exist
     */
    public function resolveVendorItemMapping($vendorId, $vendorItemCode, $itemData = null)
    {
        \Log::info('GRNService: Resolving vendor item mapping', [
            'vendor_id' => $vendorId,
            'vendor_item_code' => $vendorItemCode
        ]);

        // Check if mapping exists
        $mapping = VendorItemMapping::where('vendor_id', $vendorId)
            ->where('vendor_item_code', $vendorItemCode)
            ->first();

        if ($mapping) {
            \Log::info('GRNService: Mapping found', ['item_id' => $mapping->item_id]);
            return Item::find($mapping->item_id);
        }

        // If no mapping exists, try to find item by item_no
        $item = Item::where('item_no', $vendorItemCode)->first();

        if (!$item && $itemData) {
            // Auto-create item if item data is provided
            \Log::info('GRNService: Auto-creating item', [
                'vendor_item_code' => $vendorItemCode,
                'description' => $itemData['description'] ?? null
            ]);

            $item = $this->createItemFromGRNData($vendorItemCode, $itemData);
        } elseif (!$item) {
            throw new \Exception("Item not found for vendor code: {$vendorItemCode}. Please create mapping.");
        }

        // Create new mapping
        VendorItemMapping::create([
            'vendor_id' => $vendorId,
            'vendor_item_code' => $vendorItemCode,
            'item_id' => $item->id,
            'vendor_item_name' => $itemData['description'] ?? null,
        ]);

        return $item;
    }

    /**
     * Calculate costs using standard business logic
     * Unit Cost = What we pay vendor (purchase cost)
     * Selling Price = What we charge customers (retail price)
     * Discount = Vendor discount % (reduces our cost)
     * Profit Margin = Selling Price - Total Cost
     */
    public function calculateCosts($unitCost, $discount, $vat, $sellingPrice, $quantity = 1)
    {
        // Apply vendor discount to our purchase cost
        $discountAmount = $unitCost * ($discount / 100);
        $discountedCost = $unitCost - $discountAmount;

        // Apply VAT to the discounted cost
        $vatAmount = $discountedCost * ($vat / 100);
        $totalCostPerUnit = $discountedCost + $vatAmount;

        // Calculate profit margin per unit
        $profitMarginAmount = $sellingPrice - $totalCostPerUnit;

        // Total cost for the quantity
        $totalCost = $totalCostPerUnit * $quantity;

        return [
            'unit_cost' => $unitCost, // Original purchase cost from vendor
            'actual_cost' => $totalCostPerUnit, // Final cost per unit (after discount + VAT)
            'profit_margin_amount' => $profitMarginAmount, // Our profit per unit
            'discount_percent' => $discount, // Vendor discount %
            'vat_amount' => $vatAmount, // VAT amount per unit
            'total_cost' => $totalCost, // Total cost for quantity
            'quantity' => $quantity,
        ];
    }

    /**
     * Validate GRN data
     */
    public function validateGRN($grnData)
    {
        if (empty($grnData['vendor_id'])) {
            throw new \Exception('Vendor is required');
        }

        if (empty($grnData['inv_no'])) {
            throw new \Exception('Invoice number is required');
        }

        if (empty($grnData['items']) || !is_array($grnData['items'])) {
            throw new \Exception('At least one item is required');
        }

        foreach ($grnData['items'] as $index => $item) {
            if (empty($item['vendor_item_code'])) {
                throw new \Exception("Vendor item code is required for item at position " . ($index + 1));
            }

            if (empty($item['unit_cost']) || $item['unit_cost'] <= 0) {
                throw new \Exception("Valid unit cost is required for item at position " . ($index + 1));
            }
        }

        return true;
    }

    /**
     * Resolve import items for Excel/CSV import
     */
    public function resolveImportItems($vendorId, $importData)
    {
        $resolved = [];
        $suggestions = [];
        $unresolved = [];

        foreach ($importData as $index => $item) {
            $vendorItemCode = $item['item_code'];

            // Add original index to maintain reference
            $item['original_index'] = $index;

            // Check for exact mapping
            $mapping = VendorItemMapping::where('vendor_id', $vendorId)
                ->where('vendor_item_code', $vendorItemCode)
                ->first();

            if ($mapping) {
                // Direct mapping found
                $item['item_id'] = $mapping->item_id;
                $item['status'] = 'mapped';
                $resolved[] = $item;
                continue;
            }

            // Try to find item by item_no
            $itemByCode = Item::where('item_no', $vendorItemCode)->first();
            if ($itemByCode) {
                // Create automatic mapping and resolve
                $this->createVendorMapping($vendorId, $vendorItemCode, $itemByCode->id, $item['description']);
                $item['item_id'] = $itemByCode->id;
                $item['status'] = 'auto_resolved';
                $resolved[] = $item;
                continue;
            }

            // Try fuzzy matching by description
            $similarItems = $this->findSimilarItems($item['description']);
            if ($similarItems->count() > 0) {
                $item['status'] = 'suggestion';
                $item['suggestions'] = $similarItems->take(3)->map(function ($suggestedItem) use ($item) {
                    return [
                        'id' => $suggestedItem->id,
                        'name' => $suggestedItem->name,
                        'item_no' => $suggestedItem->item_no,
                        'category' => $suggestedItem->category->name ?? null,
                        'similarity_score' => $this->calculateSimilarity($item['description'], $suggestedItem->name)
                    ];
                })->toArray();
                $suggestions[] = $item;
                continue;
            }

            // No matches found - needs manual resolution
            $item['status'] = 'unresolved';
            $unresolved[] = $item;
        }

        return [
            'resolved' => $resolved,
            'suggestions' => $suggestions,
            'unresolved' => $unresolved
        ];
    }

    /**
     * Create vendor item mapping
     */
    public function createVendorMapping($vendorId, $vendorItemCode, $itemId, $description = null)
    {
        return VendorItemMapping::create([
            'vendor_id' => $vendorId,
            'vendor_item_code' => $vendorItemCode,
            'item_id' => $itemId,
            'vendor_item_name' => $description // Use vendor_item_name instead of description
        ]);
    }

    /**
     * Create new item from GRN data
     */
    public function createItemFromGRNData($vendorItemCode, $itemData)
    {
        return Item::create([
            'name' => $itemData['description'] ?? $vendorItemCode,
            'item_no' => $vendorItemCode,
            'description' => $itemData['description'] ?? null,
            'category_id' => $this->getDefaultCategoryId(),
            'unit_of_measure' => 'pcs',
            'reorder_point' => 0,
            'is_serialized' => false,
            'is_active' => true,
        ]);
    }

    /**
     * Create new item from import data
     */
    public function createItemFromImport($itemData)
    {
        // Generate item number if not provided
        $itemNo = $itemData['item_no'] ?? $this->generateItemNumber();

        return Item::create([
            'name' => $itemData['name'],
            'item_no' => $itemNo,
            'description' => $itemData['description'] ?? null,
            'category_id' => $itemData['category_id'] ?? $this->getDefaultCategoryId(),
            'unit_of_measure' => $itemData['unit_of_measure'] ?? 'pcs',
            'reorder_point' => $itemData['reorder_point'] ?? 0, // Set default to 0 instead of null
            'is_serialized' => $itemData['is_serialized'] ?? false,
            'is_active' => true,
        ]);
    }

    /**
     * Find similar items by name/description using fuzzy matching
     */
    private function findSimilarItems($description)
    {
        $searchTerms = $this->extractSearchTerms($description);

        $query = Item::where('is_active', true);

        foreach ($searchTerms as $term) {
            $query->orWhere('name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
        }

        return $query->with('category')->get();
    }

    /**
     * Extract meaningful search terms from description
     */
    private function extractSearchTerms($description)
    {
        // Remove common words and split into meaningful terms
        $commonWords = ['the', 'and', 'or', 'for', 'with', 'a', 'an', 'in', 'on', 'at', 'to', 'from'];
        $words = explode(' ', strtolower($description));

        return array_filter($words, function($word) use ($commonWords) {
            return strlen($word) > 2 && !in_array($word, $commonWords);
        });
    }

    /**
     * Calculate similarity score between two strings
     */
    private function calculateSimilarity($str1, $str2)
    {
        $similarity = 0;
        similar_text(strtolower($str1), strtolower($str2), $similarity);
        return round($similarity, 2);
    }

    /**
     * Generate unique item number
     */
    private function generateItemNumber()
    {
        do {
            $itemNo = 'ITM' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Item::where('item_no', $itemNo)->exists());

        return $itemNo;
    }

    /**
     * Get default category ID (create one if none exists)
     */
    private function getDefaultCategoryId()
    {
        $category = Category::firstOrCreate(
            ['name' => 'General'],
            ['description' => 'Default category for imported items']
        );

        return $category->id;
    }

    /**
     * Resolve bin location by code, create if doesn't exist
     */
    public function resolveBinLocation($storeId, $binCode)
    {
        if (empty($binCode)) {
            return null;
        }

        // Normalize bin code (trim whitespace, convert to uppercase)
        $binCode = strtoupper(trim($binCode));

        // Try to find existing bin in the store
        $bin = Bin::where('store_id', $storeId)
            ->where('code', $binCode)
            ->first();

        if ($bin) {
            \Log::info('GRNService: Found existing bin', ['bin_id' => $bin->id, 'code' => $binCode]);
            return $bin->id;
        }

        // Create new bin if not found
        \Log::info('GRNService: Creating new bin', ['store_id' => $storeId, 'code' => $binCode]);

        $newBin = Bin::create([
            'store_id' => $storeId,
            'code' => $binCode,
            'name' => $binCode, // Use code as name by default
            'description' => 'Auto-created during GRN processing',
            'is_active' => true,
        ]);

        return $newBin->id;
    }
}