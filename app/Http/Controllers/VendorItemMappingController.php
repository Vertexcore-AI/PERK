<?php

namespace App\Http\Controllers;

use App\Models\VendorItemMapping;
use App\Models\Vendor;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorItemMappingController extends Controller
{
    /**
     * Display a listing of vendor item mappings.
     */
    public function index(Request $request)
    {
        $query = VendorItemMapping::with(['vendor', 'item.category']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_item_code', 'like', "%{$search}%")
                  ->orWhere('vendor_item_name', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function ($itemQuery) use ($search) {
                      $itemQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('item_no', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by preferred status
        if ($request->filled('preferred')) {
            $query->where('is_preferred', $request->preferred === '1');
        }

        $mappings = $query->latest()->paginate(15);

        $vendors = Vendor::all();
        $totalMappings = VendorItemMapping::count();
        $preferredMappings = VendorItemMapping::where('is_preferred', true)->count();
        $uniqueItems = VendorItemMapping::distinct('item_id')->count();
        $averageMappingsPerItem = $uniqueItems > 0 ? round($totalMappings / $uniqueItems, 1) : 0;

        return view('vendor-item-mappings.index', compact(
            'mappings',
            'vendors',
            'totalMappings',
            'preferredMappings',
            'uniqueItems',
            'averageMappingsPerItem'
        ));
    }

    /**
     * Show the form for creating a new mapping.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->with('category')->get();

        return view('vendor-item-mappings.create', compact('vendors', 'items'));
    }

    /**
     * Store a newly created mapping.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'item_id' => 'required|exists:items,id',
            'vendor_item_code' => 'required|string|max:50',
            'vendor_item_name' => 'nullable|string|max:255',
            'vendor_cost' => 'nullable|numeric|min:0',
            'is_preferred' => 'boolean',
        ], [
            'vendor_item_code.required' => 'Vendor item code is required',
            'vendor_id.required' => 'Please select a vendor',
            'item_id.required' => 'Please select an item',
        ]);

        // Check for duplicate mapping
        if (VendorItemMapping::mappingExists($request->vendor_id, $request->vendor_item_code)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A mapping for this vendor item code already exists.');
        }

        DB::transaction(function () use ($request) {
            $mapping = VendorItemMapping::create([
                'vendor_id' => $request->vendor_id,
                'item_id' => $request->item_id,
                'vendor_item_code' => $request->vendor_item_code,
                'vendor_item_name' => $request->vendor_item_name,
                'vendor_cost' => $request->vendor_cost,
                'is_preferred' => $request->boolean('is_preferred', false),
            ]);

            // If marked as preferred, update other mappings
            if ($request->boolean('is_preferred')) {
                $mapping->setAsPreferred();
            }
        });

        return redirect()->route('inventory.mappings.index')
            ->with('success', 'Vendor item mapping created successfully.');
    }

    /**
     * Display the specified mapping.
     */
    public function show(VendorItemMapping $mapping)
    {
        $mapping->load(['vendor', 'item.category']);

        // Get other mappings for the same item
        $otherMappings = VendorItemMapping::where('item_id', $mapping->item_id)
            ->where('id', '!=', $mapping->id)
            ->with('vendor')
            ->get();

        return view('vendor-item-mappings.show', compact('mapping', 'otherMappings'));
    }

    /**
     * Show the form for editing the mapping.
     */
    public function edit(VendorItemMapping $mapping)
    {
        $vendors = Vendor::all();
        $items = Item::where('is_active', true)->with('category')->get();

        return view('vendor-item-mappings.edit', compact('mapping', 'vendors', 'items'));
    }

    /**
     * Update the specified mapping.
     */
    public function update(Request $request, VendorItemMapping $mapping)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'item_id' => 'required|exists:items,id',
            'vendor_item_code' => 'required|string|max:50',
            'vendor_item_name' => 'nullable|string|max:255',
            'vendor_cost' => 'nullable|numeric|min:0',
            'is_preferred' => 'boolean',
        ]);

        // Check for duplicate mapping (excluding current mapping)
        $existingMapping = VendorItemMapping::where('vendor_id', $request->vendor_id)
            ->where('vendor_item_code', $request->vendor_item_code)
            ->where('id', '!=', $mapping->id)
            ->first();

        if ($existingMapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A mapping for this vendor item code already exists.');
        }

        DB::transaction(function () use ($request, $mapping) {
            $mapping->update([
                'vendor_id' => $request->vendor_id,
                'item_id' => $request->item_id,
                'vendor_item_code' => $request->vendor_item_code,
                'vendor_item_name' => $request->vendor_item_name,
                'vendor_cost' => $request->vendor_cost,
                'is_preferred' => $request->boolean('is_preferred', false),
            ]);

            // If marked as preferred, update other mappings
            if ($request->boolean('is_preferred')) {
                $mapping->setAsPreferred();
            }
        });

        return redirect()->route('inventory.mappings.index')
            ->with('success', 'Vendor item mapping updated successfully.');
    }

    /**
     * Remove the specified mapping.
     */
    public function destroy(VendorItemMapping $mapping)
    {
        $mapping->delete();

        return redirect()->route('inventory.mappings.index')
            ->with('success', 'Vendor item mapping deleted successfully.');
    }

    /**
     * Set mapping as preferred
     */
    public function setPreferred(VendorItemMapping $mapping)
    {
        $mapping->setAsPreferred();

        return response()->json([
            'success' => true,
            'message' => 'Mapping set as preferred vendor for this item.'
        ]);
    }

    /**
     * Search items for autocomplete
     */
    public function searchItems(Request $request)
    {
        $search = $request->get('search', '');

        $items = Item::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('item_no', 'like', "%{$search}%");
            })
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json($items->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name . ' (' . $item->item_no . ')',
                'item_no' => $item->item_no,
                'category' => $item->category->name ?? 'Uncategorized',
            ];
        }));
    }

    /**
     * Bulk import mappings
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'mappings' => 'required|array',
            'mappings.*.vendor_item_code' => 'required|string',
            'mappings.*.item_id' => 'required|exists:items,id',
            'mappings.*.vendor_cost' => 'nullable|numeric|min:0',
        ]);

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($request, &$createdCount, &$skippedCount) {
            foreach ($request->mappings as $mappingData) {
                if (!VendorItemMapping::mappingExists($request->vendor_id, $mappingData['vendor_item_code'])) {
                    VendorItemMapping::create([
                        'vendor_id' => $request->vendor_id,
                        'item_id' => $mappingData['item_id'],
                        'vendor_item_code' => $mappingData['vendor_item_code'],
                        'vendor_cost' => $mappingData['vendor_cost'] ?? null,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Created {$createdCount} mappings, skipped {$skippedCount} duplicates.",
            'created' => $createdCount,
            'skipped' => $skippedCount,
        ]);
    }
}