<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'loan_duration_days',
        'due_date',
        'due_time',
        'condition',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'loan_duration_days' => 'integer',
        'due_date' => 'date',
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

    public function lossReports(): HasMany
    {
        return $this->hasMany(self::class, 'borrow_transaction_id')
            ->where('type', 'loss_report')
            ->latest('transaction_date');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('type', 'borrow')
            ->where('status', 'Dipinjam')
            ->whereNotNull('due_date')
            ->where(function (Builder $query) use ($now) {
                $query->whereDate('due_date', '<', $now->toDateString())
                    ->orWhere(function (Builder $query) use ($now) {
                        $query->whereDate('due_date', $now->toDateString())
                            ->whereNotNull('due_time')
                            ->whereTime('due_time', '<', $now->format('H:i:s'));
                    });
            });
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

    public function getFormattedDueDateAttribute(): string
    {
        if (!$this->due_date) {
            return '-';
        }

        return $this->due_date->format('d-m-Y')
            . ($this->due_time ? ' ' . substr($this->due_time, 0, 5) : '');
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->type !== 'borrow' || $this->status !== 'Dipinjam' || !$this->due_date) {
            return false;
        }

        if (!$this->due_time) {
            return $this->due_date->isBefore(today());
        }

        $dueDateTime = $this->due_date->copy();
        $dueDateTime->setTimeFromTimeString($this->due_time);

        return $dueDateTime->isBefore(now());
    }

    public function getCleanNotesAttribute(): string
    {
        if (!$this->notes) {
            return '-';
        }

        return preg_replace('/Pengembalian:\s*\d{4}[-\/]\d{2}[-\/]\d{2},?\s*(oleh\s*)?/i', 'Pengembalian oleh ', $this->notes) ?: '-';
    }
}
