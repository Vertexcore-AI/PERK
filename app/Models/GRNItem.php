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
        'received_qty',
        'unit_price',
        'discount',
        'unit_cost',
        'vat',
        'total_cost',
        'stored_qty',
        'notes',
    ];

    protected $casts = [
        'received_qty' => 'integer',
        'stored_qty' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'unit_cost' => 'decimal:2',
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
        $this->unit_cost = $this->unit_price - ($this->unit_price * $this->discount / 100);
        $costBeforeVat = $this->unit_cost * $this->received_qty;
        $this->total_cost = $costBeforeVat + ($costBeforeVat * $this->vat / 100);
    }
}