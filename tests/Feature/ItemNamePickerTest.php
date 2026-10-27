<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemNamePickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_lists_distinct_active_item_names_and_accepts_new_names(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        foreach (['INV-100', 'INV-101'] as $code) {
            Item::create(['code' => $code, 'name' => 'Kursi Kantor', 'brand' => 'Olympic', 'quantity' => 1, 'status' => 'Tersedia']);
        }
        Item::create(['code' => 'INV-102', 'name' => 'Barang Arsip', 'quantity' => 1, 'status' => 'Tersedia', 'is_active' => false]);

        $this->actingAs($admin)->get(route('items.create'))
            ->assertOk()
            ->assertViewHas('itemNames', fn ($names) => $names->all() === ['Kursi Kantor'])
            ->assertViewHas('itemBrands', fn ($brands) => $brands->all() === ['Olympic'])
            ->assertSee('data-item-brand="Olympic"', false)
            ->assertSee('data-item-name="Kursi Kantor"', false)
            ->assertDontSee('Barang Arsip');

        $this->post(route('items.store'), [
            'name' => 'Meja Baru',
            'brand' => 'Merek Baru',
            'quantity' => 2,
            'unit' => 'pcs',
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ])->assertSessionHasNoErrors()->assertRedirect(route('items.index'));

        $this->assertEquals(2, Item::where('name', 'Meja Baru')->count());
        $this->assertDatabaseHas('items', ['name' => 'Meja Baru', 'brand' => 'Merek Baru', 'quantity' => 1]);
    }
}
