<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ItemInventoryStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_damage_and_transfer_statuses_are_normalized(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $timestamps = ['created_at' => now(), 'updated_at' => now()];
        $damagedItemId = DB::table('items')->insertGetId(array_merge($timestamps, [
            'code' => 'LEGACY-DAMAGED',
            'name' => 'Barang Rusak Lama',
            'quantity' => 1,
            'condition' => 'Rusak',
            'status' => 'Rusak',
            'is_active' => true,
        ]));
        $transferredItemId = DB::table('items')->insertGetId(array_merge($timestamps, [
            'code' => 'LEGACY-TRANSFERRED',
            'name' => 'Barang Dipindahkan Lama',
            'quantity' => 1,
            'condition' => 'Baik',
            'status' => 'Dipindahkan',
            'is_active' => true,
        ]));
        $borrowedItemId = DB::table('items')->insertGetId(array_merge($timestamps, [
            'code' => 'LEGACY-TRANSFERRED-BORROWED',
            'name' => 'Barang Dipinjam Lama',
            'quantity' => 1,
            'condition' => 'Baik',
            'status' => 'Dipindahkan',
            'is_active' => true,
        ]));
        Transaction::create([
            'item_id' => $borrowedItemId,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $migration = include database_path('migrations/2026_11_15_000001_normalize_item_inventory_statuses.php');
        $migration->up();

        $this->assertDatabaseHas('items', [
            'id' => $damagedItemId,
            'condition' => 'Rusak',
            'status' => 'Tersedia',
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $transferredItemId,
            'status' => 'Tersedia',
        ]);
        $this->assertDatabaseHas('items', [
            'id' => $borrowedItemId,
            'status' => 'Dipinjam',
        ]);
    }

    public function test_condition_values_do_not_change_inventory_status(): void
    {
        $item = Item::create([
            'code' => 'CONDITION-SEPARATION',
            'name' => 'Barang Uji Kondisi',
            'quantity' => 1,
            'condition' => 'Rusak',
            'status' => 'Tersedia',
        ]);

        $this->assertSame('Rusak', $item->fresh()->condition);
        $this->assertSame('Tersedia', $item->fresh()->status);
    }
}
