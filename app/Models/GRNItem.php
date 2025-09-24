<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GRNItem extends Model
{
    protected $table = 'grn_items';
    protected $primaryKey = 'grn_item_id';

    protected $fillable = [
        'grn_id',
        'item_id',
        'batch_id',
        'vendor_item_code',
        'quantity',
        'unit_cost',
        'selling_price',
        'discount',
        'vat',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function grn(): BelongsTo
    {
        return $this->belongsTo(GRN::class, 'grn_id', 'grn_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function calculateCosts()
    {
        // Calculate actual amounts from percentages
        $discountAmount = $this->unit_cost * ($this->discount / 100);
        $vatAmount = $this->unit_cost * ($this->vat / 100);

        // total_cost = (unit_cost after discount) * quantity (what we actually paid)
        $this->total_cost = ($this->unit_cost - $discountAmount) * ($this->quantity ?? 1);

        // selling_price = unit_cost + vat_amount - discount_amount (per unit)
        $this->selling_price = $this->unit_cost + $vatAmount - $discountAmount;
    }
}