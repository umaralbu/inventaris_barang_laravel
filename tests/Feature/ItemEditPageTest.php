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
            ->assertSee('Barang Uji Edit')
            ->assertSee('Ruang Aktif')
            ->assertSee('Ruang Arsip Lama');
    }
}