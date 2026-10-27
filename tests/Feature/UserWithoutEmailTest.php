<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserWithoutEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_users_without_email_and_user_can_log_in(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin);

        foreach (['pegawai_baru', 'pegawai_lain'] as $username) {
            $this->post(route('users.store'), [
                'name' => 'Pegawai Baru',
                'username' => $username,
                'password' => 'rahasia123',
                'role' => User::ROLE_USER,
            ])->assertSessionHasNoErrors()->assertRedirect(route('users.index'));
            $this->assertDatabaseHas('users', ['username' => $username, 'email' => null]);
        }

        $user = User::where('username', 'pegawai_baru')->firstOrFail();
        $this->put(route('users.update', $user), [
            'name' => 'Pegawai Diperbarui',
            'username' => 'pegawai_baru',
            'role' => User::ROLE_USER,
        ])->assertSessionHasNoErrors()->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Pegawai Diperbarui']);

        foreach (['users.index', 'users.create', 'users.edit', 'users.show'] as $route) {
            $this->get(route($route, $user))->assertOk()->assertDontSee('Email');
        }

        $this->post(route('logout'));
        $this->post(route('login'), [
            'username' => 'pegawai_baru',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
