<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $primaryKey = 'quote_id';

    protected $fillable = [
        'customer_id',
        'quote_date',
        'valid_until',
        'total_estimate',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
