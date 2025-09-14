<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $fillable = [
        'store_id',
        'code',
        'name',
        'description',
        'is_active'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}