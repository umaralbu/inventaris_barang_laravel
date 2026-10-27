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

    public function test_editing_item_preserves_quantity_and_unit(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::create([
            'code' => 'INV-EDIT-2',
            'name' => 'Barang Lama',
            'quantity' => 7,
            'unit' => 'unit',
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ]);

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
