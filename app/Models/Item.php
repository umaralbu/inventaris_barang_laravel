<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'location_id',
        'code',
        'name',
        'brand',
        'specification',
        'quantity',
        'unit',
        'condition',
        'status',
        'is_active',
        'purchase_date',
        'purchase_price',
        'description',
        'photo',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public static function generateCode(): string
    {
        $sequence = 1;

        do {
            $code = 'INV-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
