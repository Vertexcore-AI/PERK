<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $primaryKey = 'bin_id';
    protected $fillable = ['store_id', 'bin_name', 'description'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }
}
