<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'contact',
        'email',
        'address',
        'company',
        'city',
        'state',
        'postal_code',
        'type',
        'vat_number',
        'is_active'
    ];

    // Relationships
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'customer_id', 'id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getTotalSpentAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('total_amount');
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->sales()->where('status', 'completed')->count();
    }
}
