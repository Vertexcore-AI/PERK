<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Quotation extends Model
{
    protected $primaryKey = 'quote_id';

    protected $fillable = [
        'customer_id',
        'quote_date',
        'valid_until',
        'total_estimate',
        'status'
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'total_estimate' => 'decimal:2'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class, 'quote_id', 'quote_id');
    }

    public function isExpired(): bool
    {
        return Carbon::now()->gt($this->valid_until) && $this->status === 'Pending';
    }

    public function canConvert(): bool
    {
        return $this->status === 'Pending' && !$this->isExpired();
    }

    public function markAsConverted(): bool
    {
        $this->status = 'Converted';
        return $this->save();
    }

    public function markAsExpired(): bool
    {
        if ($this->isExpired()) {
            $this->status = 'Expired';
            return $this->save();
        }
        return false;
    }

    public function calculateTotal(): float
    {
        return $this->quoteItems->sum('total');
    }
}
