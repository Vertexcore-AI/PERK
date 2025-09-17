<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'customer_id',
        'sale_date',
        'total_amount',
        'payment_method',
        'cash_amount',
        'card_amount',
        'status',
        'notes',
        'discount_percentage',
        'discount_amount',
        'vat_percentage',
        'vat_amount',
        'subtotal'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'sale_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_date', [$startDate, $endDate]);
    }

    // Accessors
    public function getProfitAttribute()
    {
        return $this->saleItems->sum(function ($item) {
            return ($item->unit_price - $item->unit_cost) * $item->quantity;
        });
    }

    public function getTotalItemsAttribute()
    {
        return $this->saleItems->sum('quantity');
    }

    public function getSubtotalAttribute()
    {
        return $this->saleItems->sum('total');
    }
}
