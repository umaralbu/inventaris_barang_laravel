<?php

namespace Tests\Feature;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DamagedItemWarehouseMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_items_are_moved_to_the_correct_storage_location(): void
    {
        $warehouse = Location::ensureActive('Gudang');
        $storage = Location::ensureActive('Ruang Penyimpanan');
        $timestamps = ['created_at' => now(), 'updated_at' => now()];

        $nonDamagedItemId = DB::table('items')->insertGetId(array_merge($timestamps, [
            'code' => 'OLD-STORAGE-1',
            'name' => 'Barang Lama',
            'location_id' => $warehouse->id,
            'quantity' => 1,
            'condition' => 'Baik',
            'status' => 'Tersedia',
            'is_active' => true,
        ]));
        $damagedItemId = DB::table('items')->insertGetId(array_merge($timestamps, [
            'code' => 'OLD-DAMAGED-1',
            'name' => 'Barang Rusak Lama',
            'location_id' => $storage->id,
            'quantity' => 1,
            'condition' => 'Rusak',
            'status' => 'Tersedia',
            'is_active' => true,
        ]));

        $migration = include database_path('migrations/2026_11_12_000002_move_damaged_items_to_warehouse.php');
        $migration->up();

        $this->assertDatabaseHas('items', [
            'id' => $nonDamagedItemId,
            'location_id' => $storage->id,
            'status' => 'Tersedia',
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $damagedItemId,
            'location_id' => $warehouse->id,
            'status' => 'Tersedia',
        ]);
    }
}
