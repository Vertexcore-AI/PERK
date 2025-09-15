<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    protected $fillable = [
        'item_id',
        'vendor_id',
        'batch_no',
        'unit_cost',
        'received_qty',
        'remaining_qty',
        'received_date',
        'expiry_date',
        'discount_percent',
        'vat_percent',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'received_qty' => 'integer',
        'remaining_qty' => 'integer',
        'discount_percent' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'received_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function grnItems(): HasMany
    {
        return $this->hasMany(GRNItem::class);
    }

    public function inventoryStock(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function serialItems(): HasMany
    {
        return $this->hasMany(SerialItem::class);
    }

    /**
     * Get available quantity in stock for this batch
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->inventoryStock()->sum('quantity');
    }

    /**
     * Check if batch has sufficient stock
     */
    public function hasSufficientStock($requiredQuantity)
    {
        return $this->available_quantity >= $requiredQuantity;
    }

    /**
     * Generate unique batch number
     */
    public static function generateBatchNumber($itemId, $vendorId)
    {
        $item = Item::find($itemId);
        $vendor = Vendor::find($vendorId);

        $prefix = strtoupper(substr($item->item_no ?? 'ITM', 0, 3));
        $vendorCode = strtoupper(substr($vendor->name ?? 'VEN', 0, 2));
        $timestamp = now()->format('ymd');

        // Find next sequence number for this combination
        $lastBatch = self::where('item_id', $itemId)
            ->where('vendor_id', $vendorId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastBatch ? (intval(substr($lastBatch->batch_no, -2)) + 1) : 1;

        return sprintf('%s-%s-%s-%02d', $prefix, $vendorCode, $timestamp, $sequence);
    }

    /**
     * Get batches for FIFO selection (oldest first)
     */
    public static function getFIFOBatches($itemId, $requiredQuantity = null)
    {
        $query = self::where('item_id', $itemId)
            ->whereHas('inventoryStock', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->with(['inventoryStock', 'vendor'])
            ->orderBy('created_at', 'asc');

        if ($requiredQuantity) {
            // Get batches until we have enough quantity
            $batches = collect();
            $remainingQty = $requiredQuantity;

            foreach ($query->get() as $batch) {
                $batches->push($batch);
                $remainingQty -= $batch->available_quantity;

                if ($remainingQty <= 0) {
                    break;
                }
            }

            return $batches;
        }

        return $query->get();
    }

    /**
     * Calculate average cost for an item across all batches
     */
    public static function getAverageCost($itemId)
    {
        return self::where('item_id', $itemId)
            ->whereHas('inventoryStock', function ($q) {
                $q->where('quantity', '>', 0);
            })
            ->avg('unit_cost') ?? 0;
    }

    /**
     * Get total value of all batches for an item
     */
    public static function getTotalValue($itemId)
    {
        return self::where('item_id', $itemId)
            ->join('inventory_stock', 'batches.id', '=', 'inventory_stock.batch_id')
            ->selectRaw('SUM(batches.unit_cost * inventory_stock.quantity) as total_value')
            ->value('total_value') ?? 0;
    }
}
