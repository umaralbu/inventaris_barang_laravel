<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemEditPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_item_edit_page_when_item_location_is_archived(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $activeLocation = Location::create(['name' => 'Ruang Aktif', 'is_active' => true]);
        $archivedLocation = Location::create(['name' => 'Ruang Arsip Lama', 'is_active' => false]);
        $item = Item::create([
            'code' => 'INV-EDIT-1',
            'name' => 'Barang Uji Edit',
            'location_id' => $archivedLocation->id,
            'quantity' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('items.edit', $item))
            ->assertOk()
            ->assertDontSee('name="quantity"', false)
            ->assertDontSee('name="unit"', false)
            ->assertSee('Barang Uji Edit')
            ->assertSee('Ruang Aktif')
            ->assertSee('Ruang Arsip Lama');
    }

<<<<<<< HEAD
    public function test_admin_can_update_item_brand_and_view_receives_brands_by_item_name(): void
=======
    public function test_editing_item_preserves_quantity_and_unit(): void
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::create([
            'code' => 'INV-EDIT-2',
<<<<<<< HEAD
            'name' => 'Mesin Penghancur Kertas',
            'brand' => 'Fellowes',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'condition' => 'Baik',
        ]);

        $response = $this->actingAs($admin)->get(route('items.edit', $item));
        $response->assertOk();
        $response->assertViewHas('brandsByItemName', fn ($map) => isset($map['Mesin Penghancur Kertas']) && in_array('Fellowes', $map['Mesin Penghancur Kertas'], true));

        $updateResponse = $this->actingAs($admin)->put(route('items.update', $item), [
            'name' => 'Mesin Penghancur Kertas',
            'brand' => 'Kozure',
            'purchase_price' => '8.500.000',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'condition' => 'Baik',
        ]);

        $updateResponse->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Mesin Penghancur Kertas',
            'brand' => 'Kozure',
            'purchase_price' => 8500000,
        ]);
    }

    public function test_updating_item_condition_to_damaged_moves_it_to_warehouse(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $warehouse = Location::ensureActive('Gudang');
        $storage = Location::ensureActive('Ruang Penyimpanan');
        $item = Item::create([
            'code' => 'INV-DAMAGED-1',
            'name' => 'Barang Rusak',
            'location_id' => $storage->id,
            'quantity' => 1,
=======
            'name' => 'Barang Lama',
            'quantity' => 7,
            'unit' => 'unit',
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ]);

<<<<<<< HEAD
        $this->actingAs($admin)->put(route('items.update', $item), [
            'name' => $item->name,
            'location_id' => $storage->id,
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'condition' => 'Rusak',
        ])->assertRedirect(route('items.index'));

        $this->assertSame($warehouse->id, $item->fresh()->location_id);
        $this->assertSame('Tersedia', $item->fresh()->status);
    }

    public function test_updating_item_condition_to_damaged_keeps_inventory_status_separate(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::create([
            'code' => 'INV-DAMAGED-BORROWED',
            'name' => 'Barang Dipinjam',
            'quantity' => 1,
            'condition' => 'Baik',
            'status' => 'Dipinjam',
        ]);

        $this->actingAs($admin)->put(route('items.update', $item), [
            'name' => $item->name,
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Dipinjam',
            'condition' => 'Rusak',
        ])->assertRedirect(route('items.index'));

        $this->assertSame('Dipinjam', $item->fresh()->status);
        $this->assertSame('Rusak', $item->fresh()->condition);
    }

    public function test_item_status_validation_rejects_condition_and_transfer_values(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::create([
            'code' => 'INV-INVALID-STATUS',
            'name' => 'Barang Status',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'condition' => 'Baik',
        ]);
        $payload = [
            'name' => $item->name,
            'quantity' => 1,
            'unit' => 'Unit',
            'condition' => 'Baik',
        ];

        $this->actingAs($admin)
            ->put(route('items.update', $item), $payload + ['status' => 'Rusak'])
            ->assertSessionHasErrors('status');

        $this->put(route('items.update', $item), $payload + ['status' => 'Dipindahkan'])
            ->assertSessionHasErrors('status');
    }

    public function test_non_damaged_item_cannot_remain_in_warehouse(): void
    {
        $warehouse = Location::ensureActive('Gudang');
        $storage = Location::ensureActive('Ruang Penyimpanan');

        $item = Item::create([
            'code' => 'INV-STORAGE-1',
            'name' => 'Barang Tidak Rusak',
            'location_id' => $warehouse->id,
            'quantity' => 1,
            'condition' => 'Baik',
        ]);

        $this->assertSame($storage->id, $item->location_id);
    }
}
=======
        foreach ([[], ['quantity' => 0, 'unit' => 'pcs']] as $extraFields) {
            $this->actingAs($admin)
                ->put(route('items.update', $item), array_merge([
                    'name' => 'Barang Diperbarui',
                    'condition' => 'Rusak',
                    'status' => 'Tersedia',
                ], $extraFields))
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('items.index'));

            $this->assertDatabaseHas('items', [
                'id' => $item->id,
                'name' => 'Barang Diperbarui',
                'condition' => 'Rusak',
                'quantity' => 7,
                'unit' => 'unit',
            ]);
        }
    }
}
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
