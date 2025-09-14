<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'store_name',
        'store_location',
    ];


    public function bins(): HasMany
    {
        return $this->hasMany(Bin::class);
    }
}
