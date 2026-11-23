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

    protected static function booted(): void
    {
        static::saving(function (Item $item) {
            if ($item->status === 'Hilang') {
                return;
            }

            if ($item->condition === 'Rusak') {
                $item->location_id = Location::ensureActive('Gudang')->id;

                return;
            }

            if ($item->location_id && Location::whereKey($item->location_id)->where('name', 'Gudang')->exists()) {
                $item->location_id = Location::ensureActive('Ruang Penyimpanan')->id;
            }

        });
    }

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

    public static function generateCodes(int $count = 1, array $exclude = []): array
    {
        $codes = [];
        $sequence = 1;
        $normalizedExclude = array_map(fn($c) => mb_strtolower(trim((string)$c)), $exclude);

        while (count($codes) < $count) {
            $code = 'INV-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
            $lower = mb_strtolower($code);

            if (in_array($lower, $normalizedExclude, true) || self::whereRaw('LOWER(code) = ?', [$lower])->exists() || in_array($code, $codes, true)) {
                continue;
            }

            $codes[] = $code;
        }

        return $codes;
    }

    public static function generateCode(): string
    {
        return self::generateCodes(1)[0];
    }
}
