<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GRN extends Model
{
    protected $table = 'grns';
    protected $primaryKey = 'grn_id';

    protected $fillable = [
        'vendor_id',
        'inv_no',
        'billing_date',
        'total_amount',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function grnItems(): HasMany
    {
        return $this->hasMany(GRNItem::class, 'grn_id', 'grn_id');
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->grnItems()->sum('total_cost');
        $this->save();
    }
}