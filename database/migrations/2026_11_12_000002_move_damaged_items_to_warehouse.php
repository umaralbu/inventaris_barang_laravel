<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $warehouseId = $this->ensureLocation('Gudang');
            $storageId = $this->ensureLocation('Ruang Penyimpanan');

            DB::table('items')
                ->where('location_id', $warehouseId)
                ->where(function ($query) {
                    $query->whereNull('condition')->orWhere('condition', '!=', 'Rusak');
                })
                ->update(['location_id' => $storageId, 'updated_at' => now()]);

            DB::table('items')
                ->where('condition', 'Rusak')
                ->where(function ($query) use ($warehouseId) {
                    $query->whereNull('location_id')->orWhere('location_id', '!=', $warehouseId);
                })
                ->update(['location_id' => $warehouseId, 'updated_at' => now()]);

        });
    }

    public function down(): void
    {
        // Location assignments are intentionally retained when rolling back.
    }

    private function ensureLocation(string $name): int
    {
        $location = DB::table('locations')->where('name', $name)->orderBy('id')->first();
        if ($location) {
            DB::table('locations')->where('id', $location->id)->update([
                'is_active' => true,
                'updated_at' => now(),
            ]);

            return (int) $location->id;
        }

        return (int) DB::table('locations')->insertGetId([
            'name' => $name,
            'description' => 'Lokasi inventaris.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
