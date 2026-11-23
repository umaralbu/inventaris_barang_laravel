<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryLocationNameSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_forms_offer_existing_names_and_allow_custom_names(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Category::create(['name' => 'Elektronik']);
        Location::create(['name' => 'Ruang Komputer', 'is_active' => true]);

        $this->actingAs($admin)
            ->get(route('categories.create'))
            ->assertOk()
            ->assertSee('id="categoryName"', false)
            ->assertSee('data-suggestion-value="Elektronik"', false)
            ->assertSee('+ Gunakan', false);

        $this->get(route('locations.create'))
            ->assertOk()
            ->assertSee('id="locationName"', false)
            ->assertSee('data-suggestion-value="Ruang Komputer"', false)
            ->assertSee('+ Gunakan', false);
    }
}
