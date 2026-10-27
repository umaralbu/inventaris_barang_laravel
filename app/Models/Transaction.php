<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'item_id',
        'borrow_transaction_id',
        'user_id',
        'type',
        'status',
        'from_location',
        'to_location',
        'from_person',
        'to_person',
        'quantity',
        'transaction_date',
        'condition',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function borrowTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'borrow_transaction_id');
    }

    public function returnTransaction(): HasOne
    {
        return $this->hasOne(self::class, 'borrow_transaction_id');
    }

    public function getEffectiveTransactionDateAttribute(): ?\Carbon\Carbon
    {
        $date = ($this->type === 'borrow' && $this->status === 'Dikembalikan' && $this->returnTransaction)
            ? $this->returnTransaction->transaction_date
            : $this->transaction_date;

        return $date;
    }

    public function getFormattedTransactionDateAttribute(): string
    {
        $date = $this->effective_transaction_date ?? $this->transaction_date;

        if (!$date) {
            return '-';
        }

        try {
            if ($date->format('H:i:s') === '00:00:00') {
                return $date->format('d-m-Y');
            }

            return $date->format('d-m-Y H:i:s');
        } catch (\Throwable) {
            return (string) $date;
        }
    }

    public function getCleanNotesAttribute(): string
    {
        if (!$this->notes) {
            return '-';
        }

        return preg_replace('/Pengembalian:\s*\d{4}[-\/]\d{2}[-\/]\d{2},?\s*(oleh\s*)?/i', 'Pengembalian oleh ', $this->notes) ?: '-';
    }
}
