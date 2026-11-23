<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchableCategoryLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_renders_searchable_category_and_location_fields_and_price_hint(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::create(['name' => 'Elektronik']);
        $location = Location::create(['name' => 'Ruang Komputer', 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('items.create'))
            ->assertOk()
            ->assertSee('id="itemCategory"', false)
            ->assertSee('id="itemCategoryValue" name="category_id" value=""', false)
            ->assertSee('data-value="' . $category->id . '"', false)
            ->assertSee('id="itemLocation"', false)
            ->assertSee('id="itemLocationValue" name="location_id" value=""', false)
            ->assertSee('data-value="' . $location->id . '"', false)
            ->assertSee('Harga harus diisi.');
    }

    public function test_edit_form_renders_searchable_category_and_location_fields_and_price_hint(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::create(['name' => 'Elektronik']);
        $location = Location::create(['name' => 'Ruang Komputer', 'is_active' => true]);
        $item = Item::create([
            'code' => 'INV-SEARCH-1',
            'name' => 'Komputer',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('items.edit', $item))
            ->assertOk()
            ->assertSee('id="itemCategory"', false)
            ->assertSee('id="itemCategoryValue" name="category_id" value="' . $category->id . '"', false)
            ->assertSee('data-value="' . $category->id . '"', false)
            ->assertSee('id="itemLocation"', false)
            ->assertSee('id="itemLocationValue" name="location_id" value="' . $location->id . '"', false)
            ->assertSee('data-value="' . $location->id . '"', false)
            ->assertSee('Harga harus diisi.');
    }
}
