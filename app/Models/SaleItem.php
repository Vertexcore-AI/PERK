<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $primaryKey = 'sale_item_id';

    protected $fillable = [
        'sale_id',
        'item_id',
        'batch_id',
        'quantity',
        'unit_price',
        'unit_cost',
        'discount',
        'vat',
        'total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'sale_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id');
    }

    // Accessors
    public function getItemProfitAttribute()
    {
        return ($this->unit_price - $this->unit_cost) * $this->quantity;
    }

    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    public function getDiscountAmountAttribute()
    {
        return $this->subtotal * ($this->discount / 100);
    }

    public function getVatAmountAttribute()
    {
        $afterDiscount = $this->subtotal - $this->discount_amount;
        return $afterDiscount * ($this->vat / 100);
    }

    public function getTotalWithoutVatAttribute()
    {
        return $this->subtotal - $this->discount_amount;
    }
}
