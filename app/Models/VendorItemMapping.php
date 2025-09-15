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
        'vendor_cost',
        'is_preferred',
    ];

    protected $casts = [
        'vendor_cost' => 'decimal:2',
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
     * Get best price vendor for an item (lowest cost)
     */
    public static function getBestPriceVendor($itemId)
    {
        return self::where('item_id', $itemId)
            ->whereNotNull('vendor_cost')
            ->orderBy('vendor_cost', 'asc')
            ->with('vendor')
            ->first();
    }

    /**
     * Compare vendor prices for an item
     */
    public static function compareVendorPrices($itemId)
    {
        return self::where('item_id', $itemId)
            ->whereNotNull('vendor_cost')
            ->with('vendor')
            ->orderBy('vendor_cost', 'asc')
            ->get()
            ->map(function ($mapping) {
                return [
                    'vendor_name' => $mapping->vendor->name,
                    'vendor_item_code' => $mapping->vendor_item_code,
                    'vendor_cost' => $mapping->vendor_cost,
                    'is_preferred' => $mapping->is_preferred,
                    'mapping_id' => $mapping->id,
                ];
            });
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
                    'actual_cost' => $batch->cost,
                    'quantity' => $batch->quantity,
                    'batch_number' => $batch->batch_number,
                    'purchase_date' => $batch->created_at,
                    'grn_id' => $batch->grnItems->first()->grn->grn_id ?? null,
                ];
            });
    }

    /**
     * Update reference cost based on recent actual purchases
     */
    public function updateReferenceFromActualCosts()
    {
        // Get average cost from last 3 batches
        $recentCost = \App\Models\Batch::where('item_id', $this->item_id)
            ->where('vendor_id', $this->vendor_id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->avg('cost');

        if ($recentCost) {
            $this->update(['vendor_cost' => round($recentCost, 2)]);
        }
    }
}
