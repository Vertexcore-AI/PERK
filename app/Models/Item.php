<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'item_no',
        'description',
        'vat',
        'manufacturer_name',
        'category_id',
        'unit_of_measure',
        'min_stock',
        'max_stock',
        'is_serialized',
    ];

    protected $casts = [
        'vat' => 'decimal:2',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'is_serialized' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function vendorItemMappings(): HasMany
    {
        return $this->hasMany(VendorItemMapping::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function inventoryStock(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }
}
