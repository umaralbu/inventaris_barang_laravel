<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsernameLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_pegawai_can_log_in_with_usernames(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Inventaris',
            'username' => 'admin',
            'email' => 'admin@inventaris.com',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'password',
        ])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post(route('logout'));

        $pegawai = User::factory()->create([
            'name' => 'Pegawai',
            'username' => 'pegawai',
            'email' => 'pegawai@inventaris.com',
            'role' => User::ROLE_USER,
        ]);

        $this->post(route('login'), [
            'username' => 'pegawai',
            'password' => 'password',
        ])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($pegawai);
    }

    public function test_login_form_requests_username_instead_of_email(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('name="username"', false)
            ->assertSee('Username')
            ->assertDontSee('name="email"', false)
            ->assertSee('data-username="admin"', false)
            ->assertSee('data-username="pegawai"', false);
    }
}