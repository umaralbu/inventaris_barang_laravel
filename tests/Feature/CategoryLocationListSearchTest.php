<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryLocationListSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_list_renders_live_search_controls_and_searchable_rows(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Category::create(['name' => 'Elektronik', 'description' => 'Kabel HDMI']);

        $this->actingAs($admin)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertSee('id="categoryListSearch"', false)
            ->assertSee('data-list-search="categoryList"', false)
            ->assertSee('id="categoryList"', false)
            ->assertSee('data-search="Elektronik Kabel HDMI"', false)
            ->assertSee('js/list-search.js')
            ->assertSee('Tidak ada kategori yang cocok dengan pencarian.');
    }

    public function test_location_list_renders_live_search_controls_and_searchable_rows(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Location::create([
            'name' => 'Ruang Komputer',
            'description' => 'Gedung Utama',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('locations.index'))
            ->assertOk()
            ->assertSee('id="locationListSearch"', false)
            ->assertSee('data-list-search="locationList"', false)
            ->assertSee('id="locationList"', false)
            ->assertSee('data-search="Ruang Komputer Gedung Utama"', false)
            ->assertSee('js/list-search.js')
            ->assertSee('Tidak ada lokasi yang cocok dengan pencarian.');
    }
}
