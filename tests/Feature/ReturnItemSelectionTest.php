<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnItemSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_return_form_lists_borrowed_items_and_borrower_names(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        Staff::create(['name' => 'Nama Staf Peminjam', 'is_active' => true]);
        $borrowedItem = Item::create([
            'code' => 'INV-RET-1',
            'name' => 'Laptop Dipinjam',
            'quantity' => 1,
            'status' => 'Dipinjam',
        ]);
        Item::create([
            'code' => 'INV-RET-2',
            'name' => 'Laptop Tersedia',
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);
        Transaction::create([
            'item_id' => $borrowedItem->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'to_person' => 'Nama Staf Peminjam',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('transactions.return'))
            ->assertOk()
            ->assertSee('INV-RET-1 - Laptop Dipinjam - dipinjam oleh Nama Staf Peminjam')
            ->assertSee('value="Nama Staf Peminjam"', false)
            ->assertDontSee('INV-RET-2');
    }

    public function test_borrow_approval_return_and_inventory_quantities_stay_linked(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $origin = Location::create(['name' => 'Gudang']);
        $destination = Location::create(['name' => 'Ruang Rapat']);
        $item = Item::create([
            'code' => 'INV-CYCLE-1',
            'name' => 'Laptop Siklus',
            'location_id' => $origin->id,
            'quantity' => 4,
            'unit' => 'unit',
            'status' => 'Tersedia',
        ]);

        $this->actingAs($user)
            ->post(route('transactions.borrow'), [
                'item_id' => $item->id,
                'staff_id' => $staff->id,
                'to_location' => 'Ruang Rapat',
                'transaction_date' => now()->toDateString(),
                'quantity' => 2,
                'purpose' => 'Kegiatan kantor',
            ])
            ->assertRedirect(route('transactions.index'));

        $borrow = Transaction::where('item_id', $item->id)->where('type', 'borrow')->firstOrFail();
        $this->assertSame('Menunggu Persetujuan', $borrow->status);
        $this->assertSame('Gudang', $borrow->from_location);
        $this->assertSame('Ruang Rapat', $borrow->to_location);
        $this->assertSame(4, $item->fresh()->quantity);

        $this->get(route('transactions.return'))
            ->assertOk()
            ->assertSee('Ada 1 permintaan pinjaman')
            ->assertDontSee('INV-CYCLE-1 - Laptop Siklus');

        $this->post(route('transactions.approve', $borrow))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('transactions.approve', $borrow))
            ->assertRedirect(route('transactions.index'));

        $this->assertSame('Dipinjam', $borrow->fresh()->status);
        $this->assertSame(2, $item->fresh()->quantity);
        $this->assertSame('Tersedia', $item->fresh()->status);
        $this->assertSame($destination->id, $item->fresh()->location_id);

        $this->delete(route('transactions.destroy', $borrow))
            ->assertSessionHasErrors('transaction');
        $this->assertDatabaseHas('transactions', ['id' => $borrow->id, 'status' => 'Dipinjam']);

        $this->actingAs($user)
            ->get(route('transactions.return'))
            ->assertOk()
            ->assertSee('INV-CYCLE-1 - Laptop Siklus - dipinjam oleh Staf Peminjam (2 unit)');

        $this->post(route('transactions.return'), [
            'borrow_transaction_id' => $borrow->id,
            'from_person' => 'Staf Peminjam',
            'to_location' => 'Gudang',
            'transaction_date' => now()->toDateString(),
            'condition' => 'Baik',
            'notes' => 'Kembali lengkap',
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame('Dikembalikan', $borrow->fresh()->status);
        $this->assertSame(4, $item->fresh()->quantity);
        $this->assertSame('Tersedia', $item->fresh()->status);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'type' => 'return',
            'borrow_transaction_id' => $borrow->id,
            'quantity' => 2,
            'from_person' => 'Staf Peminjam',
        ]);

        $return = Transaction::where('borrow_transaction_id', $borrow->id)->firstOrFail();
        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Hapus tracking pengembalian')
            ->assertSee('Pengembalian oleh Staf Peminjam, ke Gudang, kondisi Baik')
            ->assertDontSee('Pengembalian: ' . now()->toDateString())
            ->assertDontSee('<td>Return</td>', false);

        $this->get(route('transactions.index', [
            'type' => 'return',
            'date' => now()->toDateString(),
        ]))
            ->assertOk()
            ->assertSee('Hapus tracking pengembalian')
            ->assertDontSee('<td>Return</td>', false);

        $this->actingAs($user)
            ->delete(route('transactions.destroy', $borrow))
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $return->id]);
        $this->assertDatabaseMissing('transactions', ['id' => $borrow->id]);
        $this->assertSame(4, $item->fresh()->quantity);
        $this->assertSame('Tersedia', $item->fresh()->status);
    }
}