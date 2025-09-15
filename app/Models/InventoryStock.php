<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStock extends Model
{
    protected $table = 'inventory_stock';
    protected $primaryKey = 'stock_id';

    protected $fillable = [
        'item_id',
        'store_id',
        'bin_id',
        'batch_id',
        'quantity',
        'last_updated',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'last_updated' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public static function updateStock($itemId, $storeId, $binId, $batchId, $quantity)
    {
        $stock = self::where('item_id', $itemId)
            ->where('store_id', $storeId)
            ->where('bin_id', $binId)
            ->where('batch_id', $batchId)
            ->first();

        if ($stock) {
            $stock->quantity += $quantity;
            $stock->last_updated = now();
            $stock->save();
        } else {
            self::create([
                'item_id' => $itemId,
                'store_id' => $storeId,
                'bin_id' => $binId,
                'batch_id' => $batchId,
                'quantity' => $quantity,
                'last_updated' => now(),
            ]);
        }
    }

    public static function getStockByItem($itemId)
    {
        return self::where('item_id', $itemId)
            ->with(['store', 'bin', 'batch'])
            ->get();
    }

    public static function getTotalStock($itemId)
    {
        return self::where('item_id', $itemId)->sum('quantity');
    }
}