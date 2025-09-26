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
        'profit_margin',
        'vat',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'profit_margin' => 'decimal:2',
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
        // Standard business logic: unit_cost = purchase cost, selling_price = retail price

        // Apply vendor discount to reduce our purchase cost
        $discountAmount = $this->unit_cost * ($this->discount / 100);
        $discountedCost = $this->unit_cost - $discountAmount;

        // Apply VAT to our discounted cost
        $vatAmount = $discountedCost * ($this->vat / 100);
        $finalCostPerUnit = $discountedCost + $vatAmount;

        // Calculate total cost for the quantity
        $this->total_cost = $finalCostPerUnit * ($this->quantity ?? 1);

        // Profit margin is the difference between selling price and final cost
        $this->profit_margin = ($this->selling_price ?? 0) - $finalCostPerUnit;
    }
}