<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $primaryKey = 'store_id';
    protected $fillable = [
        'store_name',
        'store_location',
    ];

    public function bins()
    {
        return $this->hasMany(Bin::class, 'store_id', 'store_id');
    }
}
