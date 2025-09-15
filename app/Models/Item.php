<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $primaryKey = 'item_id';

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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
