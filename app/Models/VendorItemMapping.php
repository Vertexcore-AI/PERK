<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorItemMapping extends Model
{
    protected $fillable = [
        'vendor_id',
        'item_id',
        'vendor_item_code',
        'vendor_item_name',
        'is_preferred',
    ];

    protected $casts = [
        'is_preferred' => 'boolean',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Find mapping by vendor and vendor item code
     */
    public static function findMapping($vendorId, $vendorItemCode)
    {
        return self::where('vendor_id', $vendorId)
            ->where('vendor_item_code', $vendorItemCode)
            ->first();
    }

    /**
     * Get all mappings for a specific item
     */
    public static function getMappingsForItem($itemId)
    {
        return self::where('item_id', $itemId)
            ->with('vendor')
            ->get();
    }

    /**
     * Get preferred vendor for an item
     */
    public static function getPreferredVendor($itemId)
    {
        return self::where('item_id', $itemId)
            ->where('is_preferred', true)
            ->with('vendor')
            ->first();
    }

    /**
     * Set as preferred vendor for an item
     */
    public function setAsPreferred()
    {
        // Remove preferred status from other mappings for this item
        self::where('item_id', $this->item_id)
            ->where('id', '!=', $this->id)
            ->update(['is_preferred' => false]);

        // Set this mapping as preferred
        $this->update(['is_preferred' => true]);
    }

    /**
     * Check if mapping already exists
     */
    public static function mappingExists($vendorId, $vendorItemCode)
    {
        return self::where('vendor_id', $vendorId)
            ->where('vendor_item_code', $vendorItemCode)
            ->exists();
    }

    /**
     * Get best price vendor for an item (based on recent batch costs)
     */
    public static function getBestPriceVendor($itemId)
    {
        // Get vendor with lowest average batch cost
        $bestVendor = \App\Models\Batch::where('item_id', $itemId)
            ->selectRaw('vendor_id, AVG(unit_cost) as avg_cost')
            ->groupBy('vendor_id')
            ->orderBy('avg_cost', 'asc')
            ->first();

        if ($bestVendor) {
            return self::where('item_id', $itemId)
                ->where('vendor_id', $bestVendor->vendor_id)
                ->with('vendor')
                ->first();
        }

        return null;
    }

    /**
     * Compare vendor prices for an item (using batch history)
     */
    public static function compareVendorPrices($itemId)
    {
        // Get average costs from batches for each vendor
        $vendorCosts = \App\Models\Batch::where('item_id', $itemId)
            ->selectRaw('vendor_id, AVG(unit_cost) as avg_cost, COUNT(*) as batch_count')
            ->groupBy('vendor_id')
            ->get()
            ->keyBy('vendor_id');

        return self::where('item_id', $itemId)
            ->with('vendor')
            ->get()
            ->map(function ($mapping) use ($vendorCosts) {
                $vendorStats = $vendorCosts->get($mapping->vendor_id);
                return [
                    'vendor_name' => $mapping->vendor->name,
                    'vendor_item_code' => $mapping->vendor_item_code,
                    'avg_cost' => $vendorStats ? round($vendorStats->avg_cost, 2) : null,
                    'batch_count' => $vendorStats ? $vendorStats->batch_count : 0,
                    'is_preferred' => $mapping->is_preferred,
                    'mapping_id' => $mapping->id,
                ];
            })
            ->sortBy('avg_cost');
    }

    /**
     * Get vendor cost history for an item (using batches for actual costs)
     */
    public static function getVendorCostHistory($itemId, $vendorId = null)
    {
        $query = \App\Models\Batch::where('item_id', $itemId)
            ->with(['vendor', 'grnItems.grn']);

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($batch) {
                return [
                    'vendor_name' => $batch->vendor->name,
                    'actual_cost' => $batch->unit_cost,
                    'quantity' => $batch->quantity,
                    'batch_number' => $batch->batch_number,
                    'purchase_date' => $batch->created_at,
                    'grn_id' => $batch->grnItems->first()->grn->grn_id ?? null,
                ];
            });
    }

}
