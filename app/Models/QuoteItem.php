<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $primaryKey = 'quote_item_id';

    protected $fillable = [
        'quote_id',
        'item_id',
        'batch_id',
        'quantity',
        'unit_price',
        'discount',
        'vat',
        'total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'quote_id', 'quote_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id');
    }

    public function calculateTotal(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        $afterDiscount = $subtotal - ($subtotal * $this->discount / 100);
        $withVat = $afterDiscount + ($afterDiscount * $this->vat / 100);
        return round($withVat, 2);
    }

    public function isStockAvailable(): bool
    {
        if (!$this->batch) {
            return false;
        }
        return $this->batch->remaining_qty >= $this->quantity;
    }
}
