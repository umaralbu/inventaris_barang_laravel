<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionAdminActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_does_not_see_borrow_and_return_buttons_in_transaction_menu(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertDontSee('Ambil Barang')
            ->assertDontSee('Kembalikan Barang')
            ->assertSee('Ekspor CSV')
            ->assertSee('Pindahkan Barang');
    }

    public function test_regular_user_sees_borrow_and_return_buttons_in_transaction_menu(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Ambil Barang')
            ->assertSee('Kembalikan Barang')
            ->assertSee('Ekspor CSV')
            ->assertSee('Pindahkan Barang');
    }

    public function test_transaction_menu_shows_item_code_column(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = \App\Models\Item::create([
            'code' => 'BRG-999',
            'name' => 'Buku Ensiklopedia',
            'quantity' => 10,
            'status' => 'Tersedia',
        ]);
        \App\Models\Transaction::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'from_location' => 'Perpustakaan',
            'to_location' => 'Ruang Baca',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('<th>Barang</th>', false)
            ->assertSee('<th>Kode Barang</th>', false)
            ->assertSee('Buku Ensiklopedia')
            ->assertSee('BRG-999');
    }
}
