<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function ensureActive(string $name): self
    {
        $location = self::firstOrCreate(
            ['name' => $name],
            ['description' => 'Lokasi inventaris.', 'is_active' => true]
        );

        if (!$location->is_active) {
            $location->update(['is_active' => true]);
        }

        return $location;
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
