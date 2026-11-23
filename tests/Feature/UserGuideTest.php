<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('guide'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_view_user_guide_from_navigation(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('guide'))
            ->assertOk()
            ->assertSee(route('guide'))
            ->assertSee('Panduan Pengguna')
            ->assertSee('Mengajukan peminjaman')
            ->assertSee('Mengembalikan barang')
            ->assertSee('Informasi penting')
            ->assertSee('Pertanyaan umum')
            ->assertSee('Barang hilang, apa yang harus dilakukan?')
            ->assertDontSee('Analitik inventaris')
            ->assertDontSee('Apa saja informasi pada halaman Analitik?')
            ->assertDontSee('Bagaimana menambah atau mengubah data barang?')
            ->assertDontSee('Kelola inventaris');
    }

    public function test_admin_can_view_admin_guide_and_management_links(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('guide'))
            ->assertOk()
            ->assertSee('Panduan Admin')
            ->assertSee('Kelola inventaris')
            ->assertSee('Kelola transaksi')
            ->assertSee(route('categories.index'))
            ->assertSee(route('locations.index'))
            ->assertSee(route('users.index'))
            ->assertSee(route('staff.index'))
            ->assertSee('Informasi penting')
            ->assertSee('Pertanyaan umum')
            ->assertSee('Bagaimana menambah atau mengubah data barang?')
            ->assertSee('Kapan pengajuan pinjaman disetujui atau ditolak?')
            ->assertSee('Analitik inventaris')
            ->assertSee('pilih periode 6, 12, atau 24 bulan.')
            ->assertSee(route('admin.analytics'))
            ->assertSee('Bagaimana mengubah rentang waktu analitik?')
            ->assertDontSee('Mengajukan peminjaman');
    }
}
