<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_topbar_contains_settings_gear_with_theme_and_logout_options(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="settingsDropdown"', false)
            ->assertSee('id="settingsBackdrop"', false)
            ->assertSee('bi-gear-fill')
            ->assertSee('id="themeToggle"', false)
            ->assertSee(route('logout'))
            ->assertSee('Logout');
    }
}
