<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'item_no',
        'name',
        'description',
        'category_id',
        'unit_cost',
        'selling_price',
        'unit_of_measure',
        'reorder_point',
        'barcode',
        'min_stock',
        'max_stock',
        'vat',
        'is_serialized',
        'is_active',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'vat' => 'decimal:2',
        'reorder_point' => 'integer',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'is_serialized' => 'boolean',
        'is_active' => 'boolean',
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
