<?php

namespace App\Http\Controllers;

use App\Models\GRN;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Store;
use App\Models\Bin;
use App\Services\GRNService;
use App\Imports\GRNExcelImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class GRNController extends Controller
{
    protected $grnService;

    public function __construct(GRNService $grnService)
    {
        $this->grnService = $grnService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grns = GRN::with(['vendor', 'grnItems'])
            ->latest()
            ->paginate(15);

        $totalGRNs = GRN::count();
        $pendingGRNs = 0; // No longer tracking stored vs received quantities
        $todayGRNs = GRN::whereDate('created_at', today())->count();
        $totalValue = GRN::sum('total_amount');

        return view('grns.index', compact('grns', 'totalGRNs', 'pendingGRNs', 'todayGRNs', 'totalValue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->get();
        $stores = Store::all();
        $bins = Bin::all();

        return view('grns.create', compact('vendors', 'items', 'stores', 'bins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'inv_no' => 'required|string|max:50',
            'billing_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.vendor_item_code' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.vat' => 'nullable|numeric|min:0|max:100',
            'items.*.store_id' => 'required|exists:stores,id',
            'items.*.bin_id' => 'nullable|exists:bins,id',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            \Log::info('GRN Creation Started', ['request_data' => $request->all()]);
            $grn = $this->grnService->processGRN($request->all());
            \Log::info('GRN Created Successfully', ['grn_id' => $grn->grn_id]);

            return redirect()->route('grns.show', $grn->grn_id)
                ->with('success', 'GRN created successfully.');
        } catch (\Exception $e) {
            \Log::error('GRN Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating GRN: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GRN $grn)
    {
        $grn->load([
            'vendor',
            'grnItems.item.category',
            'grnItems.batch',
        ]);

        return view('grns.show', compact('grn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GRN $grn)
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->get();
        $stores = Store::all();
        $bins = Bin::all();

        $grn->load('grnItems.item');

        return view('grns.edit', compact('grn', 'vendors', 'items', 'stores', 'bins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GRN $grn)
    {
        $request->validate([
            'billing_date' => 'required|date',
        ]);

        $grn->update([
            'billing_date' => $request->billing_date,
        ]);

        return redirect()->route('grns.show', $grn->grn_id)
            ->with('success', 'GRN updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GRN $grn)
    {
        // GRNs can now be deleted since we no longer track stored quantities separately

        $grn->delete();

        return redirect()->route('grns.index')
            ->with('success', 'GRN deleted successfully.');
    }


    /**
     * Upload and parse Excel file for GRN import
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('excel_file');
            $import = new GRNExcelImport();

            // Parse the Excel file
            $collection = Excel::toCollection($import, $file)->first();

            // Convert to array and clean data
            $importData = [];
            foreach ($collection as $rowIndex => $row) {
                // Normalize column names for each row
                $normalizedRow = [];
                foreach ($row as $key => $value) {
                    $normalizedKey = $this->normalizeColumnName($key);
                    $normalizedRow[$normalizedKey] = $value;
                }

                // Check if we have required columns
                if (!empty($normalizedRow['item_code']) && !empty($normalizedRow['description'])) {
                    // Calculate default selling price if not provided
                    $unitCost = (float) ($normalizedRow['unit_cost'] ?? 0);
                    $sellingPrice = (float) ($normalizedRow['selling_price'] ?? 0);

                    // If no selling price provided, calculate using the correct formula
                    if ($sellingPrice == 0 && $unitCost > 0) {
                        $vat = (float) ($normalizedRow['vat'] ?? 0);
                        $discount = (float) ($normalizedRow['discount'] ?? 0);
                        $vatAmount = $unitCost * ($vat / 100);
                        $discountAmount = $unitCost * ($discount / 100);
                        $sellingPrice = $unitCost + $vatAmount - $discountAmount;
                    }

                    $importData[] = [
                        'item_code' => trim($normalizedRow['item_code']),
                        'description' => trim($normalizedRow['description']),
                        'quantity' => (int) ($normalizedRow['quantity'] ?? 1),
                        'unit_cost' => $unitCost,
                        'selling_price' => round($sellingPrice, 2),
                        'vat' => (float) ($normalizedRow['vat'] ?? 0),
                        'discount' => (float) ($normalizedRow['discount'] ?? 0),
                    ];
                }
            }

            if (empty($importData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid data found in the Excel file.'
                ]);
            }

            // Resolve mappings using existing GRN service
            $resolvedData = $this->grnService->resolveImportItems($request->vendor_id, $importData);

            // Store in session for processing
            session([
                'grn_import_data' => $resolvedData,
                'grn_vendor_id' => $request->vendor_id
            ]);

            return response()->json([
                'success' => true,
                'data' => $resolvedData,
                'stats' => [
                    'total' => count($importData),
                    'resolved' => count($resolvedData['resolved']),
                    'suggestions' => count($resolvedData['suggestions']),
                    'unresolved' => count($resolvedData['unresolved'])
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Excel import failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName() ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing Excel file: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Resolve a mapping suggestion during import
     */
    public function resolveMapping(Request $request)
    {
        // Force JSON response even on validation errors
        $request->headers->set('Accept', 'application/json');

        try {
            $request->validate([
                'row_index' => 'required|integer',
                'action' => 'required|in:map_existing,create_new',
                'item_id' => 'required_if:action,map_existing|exists:items,id',
                'item_data' => 'required_if:action,create_new'
            ]);

            // Parse item_data if it's a JSON string
            $itemData = null;
            if ($request->has('item_data') && is_string($request->item_data)) {
                $itemData = json_decode($request->item_data, true);
                if (!$itemData) {
                    return response()->json(['success' => false, 'message' => 'Invalid item data format.']);
                }
            } else if ($request->has('item_data')) {
                $itemData = $request->item_data;
            }

            \Log::info('Resolve mapping request', [
                'row_index' => $request->row_index,
                'action' => $request->action,
                'item_id' => $request->item_id,
                'item_data' => $itemData ?? null
            ]);

            $importData = session('grn_import_data');
            if (!$importData) {
                \Log::error('No import session found');
                return response()->json(['success' => false, 'message' => 'No import session found.']);
            }

            $rowIndex = $request->row_index;
            $action = $request->action;

            // Find the row in suggestions or unresolved
            // We need to search by the actual item data, not just index, since indices can change
            $targetRow = null;
            $sourceArray = null;
            $actualIndex = null;

            // First check suggestions
            foreach ($importData['suggestions'] ?? [] as $idx => $item) {
                if ($idx == $rowIndex ||
                    (isset($item['original_index']) && $item['original_index'] == $rowIndex)) {
                    $targetRow = $item;
                    $sourceArray = 'suggestions';
                    $actualIndex = $idx;
                    break;
                }
            }

            // If not found in suggestions, check unresolved
            if (!$targetRow) {
                foreach ($importData['unresolved'] ?? [] as $idx => $item) {
                    if ($idx == $rowIndex ||
                        (isset($item['original_index']) && $item['original_index'] == $rowIndex)) {
                        $targetRow = $item;
                        $sourceArray = 'unresolved';
                        $actualIndex = $idx;
                        break;
                    }
                }
            }

            if (!$targetRow) {
                \Log::warning('Row not found', [
                    'row_index' => $rowIndex,
                    'suggestions_count' => count($importData['suggestions'] ?? []),
                    'unresolved_count' => count($importData['unresolved'] ?? [])
                ]);
                return response()->json(['success' => false, 'message' => 'Row not found.']);
            }

            switch ($action) {
                case 'map_existing':
                    // Create mapping and move to resolved
                    $this->grnService->createVendorMapping(
                        session('grn_vendor_id'),
                        $targetRow['item_code'],
                        $request->item_id,
                        $targetRow['description']
                    );

                    $targetRow['item_id'] = $request->item_id;
                    // Preserve selling price if provided
                    if ($request->has('selling_price')) {
                        $targetRow['selling_price'] = (float) $request->selling_price;
                    }
                    $importData['resolved'][] = $targetRow;
                    break;

                case 'create_new':
                    // Create new item and mapping
                    $newItem = $this->grnService->createItemFromImport($itemData);
                    $this->grnService->createVendorMapping(
                        session('grn_vendor_id'),
                        $targetRow['item_code'],
                        $newItem->id,
                        $targetRow['description']
                    );

                    $targetRow['item_id'] = $newItem->id;
                    // Preserve selling price if provided
                    if ($request->has('selling_price')) {
                        $targetRow['selling_price'] = (float) $request->selling_price;
                    }
                    $importData['resolved'][] = $targetRow;
                    break;

            }

            // Remove from original array using the actual index
            unset($importData[$sourceArray][$actualIndex]);
            $importData[$sourceArray] = array_values($importData[$sourceArray]);

            // Update session
            session(['grn_import_data' => $importData]);

            return response()->json([
                'success' => true,
                'data' => $importData,
                'stats' => [
                    'resolved' => count($importData['resolved']),
                    'suggestions' => count($importData['suggestions']),
                    'unresolved' => count($importData['unresolved']),
                    'total' => count($importData['resolved']) + count($importData['suggestions']) + count($importData['unresolved'])
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in resolve mapping', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'validation_rules' => [
                    'row_index' => 'required|integer',
                    'action' => 'required|in:map_existing,create_new',
                    'item_id' => 'required_if:action,map_existing|exists:items,id',
                    'item_data' => 'required_if:action,create_new'
                ]
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(function($errors) { return implode(', ', $errors); }, $e->errors())),
                'errors' => $e->errors(),
                'debug' => [
                    'received_action' => $request->action,
                    'received_row_index' => $request->row_index,
                    'received_item_id' => $request->item_id,
                    'has_item_data' => $request->has('item_data')
                ]
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Resolve mapping error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Process the resolved import data and create GRN
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'inv_no' => 'required|string|max:50',
            'billing_date' => 'required|date',
        ]);

        try {
            $importData = session('grn_import_data');
            if (!$importData || empty($importData['resolved'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No resolved items found to process.'
                ]);
            }

            // Get the default store dynamically
            $defaultStore = \App\Models\Store::first();
            if (!$defaultStore) {
                return response()->json([
                    'success' => false,
                    'message' => 'No stores available. Please create a store first.'
                ]);
            }

            // Prepare GRN data in the format expected by existing GRNService
            $grnData = [
                'vendor_id' => $request->vendor_id,
                'inv_no' => $request->inv_no,
                'billing_date' => $request->billing_date,
                'items' => []
            ];

            foreach ($importData['resolved'] as $item) {
                $grnData['items'][] = [
                    'vendor_item_code' => $item['item_code'],
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_cost' => $item['unit_cost'],
                    'selling_price' => $item['selling_price'] ?? ($item['unit_cost'] + ($item['unit_cost'] * 0.3)), // Use imported selling price or default calculation
                    'discount' => $item['discount'] ?? 0,
                    'vat' => $item['vat'] ?? 0,
                    'store_id' => $request->default_store_id ?? $defaultStore->id, // Use provided store or first available
                ];
            }

            // Use existing GRN processing logic
            $grn = $this->grnService->processGRN($grnData);

            // Clear import session
            session()->forget(['grn_import_data', 'grn_vendor_id']);

            return response()->json([
                'success' => true,
                'grn_id' => $grn->grn_id,
                'message' => 'GRN created successfully from import.',
                'redirect' => route('grns.show', $grn->grn_id)
            ]);

        } catch (\Exception $e) {
            \Log::error('Import processing failed', [
                'error' => $e->getMessage(),
                'vendor_id' => $request->vendor_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing import: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download Excel template for GRN import
     */
    public function downloadTemplate()
    {
        $headers = GRNExcelImport::getExpectedHeaders();
        $sampleData = GRNExcelImport::getSampleData();

        return Excel::download(new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray {
            private $headers;
            private $data;

            public function __construct($headers, $data) {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function array(): array {
                return array_merge([$this->headers], $this->data);
            }
        }, 'grn_import_template.xlsx');
    }

    /**
     * Normalize column names to handle variations
     */
    private function normalizeColumnName($columnName)
    {
        $columnName = strtolower(trim($columnName));

        // Map common variations to our expected format
        $mappings = [
            'item code' => 'item_code',
            'itemcode' => 'item_code',
            'item_code' => 'item_code',
            'vendor item code' => 'item_code',
            'vendor_item_code' => 'item_code',
            'part number' => 'item_code',
            'part_number' => 'item_code',

            'description' => 'description',
            'item description' => 'description',
            'item_description' => 'description',
            'name' => 'description',
            'item name' => 'description',
            'item_name' => 'description',

            'unit price' => 'unit_cost',
            'unitprice' => 'unit_cost',
            'unit_price' => 'unit_cost',
            'unit cost' => 'unit_cost',
            'unit_cost' => 'unit_cost',
            'price' => 'unit_cost',
            'cost' => 'unit_cost',
            'purchase price' => 'unit_cost',

            'selling price' => 'selling_price',
            'selling_price' => 'selling_price',
            'sellingprice' => 'selling_price',
            'sale price' => 'selling_price',
            'sale_price' => 'selling_price',
            'retail price' => 'selling_price',
            'retail_price' => 'selling_price',

            'quantity' => 'quantity',
            'qty' => 'quantity',
            'received quantity' => 'quantity',
            'received_quantity' => 'quantity',
            'received qty' => 'quantity',
            'received_qty' => 'quantity',

            'vat' => 'vat',
            'vat%' => 'vat',
            'vat percent' => 'vat',
            'vat_percent' => 'vat',
            'tax' => 'vat',

            'discount' => 'discount',
            'discount%' => 'discount',
            'discount percent' => 'discount',
            'discount_percent' => 'discount',
        ];

        return $mappings[$columnName] ?? $columnName;
    }
}