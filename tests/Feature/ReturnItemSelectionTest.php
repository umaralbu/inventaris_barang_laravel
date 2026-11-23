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
        $origin = Location::create(['name' => 'Gudang Asal', 'is_active' => true]);
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
        $borrow = Transaction::create([
            'item_id' => $borrowedItem->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'to_person' => 'Nama Staf Peminjam',
            'from_location' => $origin->name,
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('transactions.return', $borrowedItem))
            ->assertOk()
            ->assertSee('INV-RET-1 - Laptop Dipinjam - dipinjam oleh Nama Staf Peminjam')
            ->assertSee('data-original-location="Gudang Asal"', false)
            ->assertSee('Lokasi awal')
            ->assertSee('class="return-location-origin-badge" data-origin-badge', false)
            ->assertSee('margin-left: auto;', false)
            ->assertSee('rgba(100, 116, 139, .62)', false)
            ->assertSee('value="Gudang Asal" data-location-label="Gudang Asal" selected', false)
            ->assertSee('value="Nama Staf Peminjam"', false)
            ->assertSee('name="condition" id="returnCondition" class="form-select" data-searchable-select data-search-placeholder=""', false)
            ->assertSee('Kondisi setelah dikembalikan')
            ->assertSee('<option selected>Baik</option>', false)
            ->assertSee('>Rusak</option>', false)
            ->assertSee('>Perlu Perbaikan</option>', false)
            ->assertDontSee('INV-RET-2');
    }

    public function test_borrow_approval_return_and_inventory_quantities_stay_linked(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $origin = Location::ensureActive('Ruang Penyimpanan');
        $destination = Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $item = Item::create([
            'code' => 'INV-CYCLE-1',
            'name' => 'Laptop Siklus',
            'location_id' => $origin->id,
            'quantity' => 1,
            'unit' => 'unit',
            'status' => 'Tersedia',
        ]);

        $this->actingAs($user)
            ->post(route('transactions.borrow'), [
                'item_ids' => [$item->id],
                'staff_id' => $staff->id,
                'item_locations' => [$item->id => 'Ruang Rapat'],
                'transaction_date' => now()->toDateString(),
                'item_due_datetimes' => [$item->id => now()->addDay()->format('Y-m-d\TH:i')],
                'purpose' => 'Kegiatan kantor',
            ])
            ->assertRedirect(route('transactions.index'));

        $borrow = Transaction::where('item_id', $item->id)->where('type', 'borrow')->firstOrFail();
        $this->assertSame('Menunggu Persetujuan', $borrow->status);
        $this->assertSame('Ruang Penyimpanan', $borrow->from_location);
        $this->assertSame('Ruang Rapat', $borrow->to_location);
        $this->assertSame(1, $item->fresh()->quantity);

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
        $this->assertSame(1, $item->fresh()->quantity);
        $this->assertSame('Dipinjam', $item->fresh()->status);
        $this->assertSame($destination->id, $item->fresh()->location_id);

        $this->delete(route('transactions.destroy', $borrow))
            ->assertSessionHasErrors('transaction');
        $this->assertDatabaseHas('transactions', ['id' => $borrow->id, 'status' => 'Dipinjam']);

        $returnForm = $this->actingAs($user)
            ->get(route('transactions.return'));
        $returnForm->assertOk()
            ->assertSee('INV-CYCLE-1 - Laptop Siklus - dipinjam oleh Staf Peminjam (1 unit)')
            ->assertSee('data-original-location="Ruang Penyimpanan"', false)
            ->assertSee('updateLocationPicker(true)', false)
            ->assertSee('data-origin-badge', false)
            ->assertSee('locationSelect.value = originalLocation', false)
            ->assertSee('Format 24 jam (HH:mm:ss)');
        $this->assertMatchesRegularExpression(
            '/<input type="time" name="transaction_time"[^>]*value="\d{2}:\d{2}:\d{2}"[^>]*step="1"/',
            $returnForm->getContent()
        );

        $this->post(route('transactions.return'), [
            'borrow_transaction_id' => $borrow->id,
            'from_person' => 'Staf Peminjam',
            'to_location' => 'Ruang Penyimpanan',
            'transaction_date' => now()->toDateString(),
            'transaction_time' => '14:05:37',
            'condition' => 'Baik',
            'notes' => 'Kembali lengkap',
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame('Dikembalikan', $borrow->fresh()->status);
        $this->assertSame(1, $item->fresh()->quantity);
        $this->assertSame('Tersedia', $item->fresh()->status);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'type' => 'return',
            'borrow_transaction_id' => $borrow->id,
            'quantity' => 1,
            'from_person' => 'Staf Peminjam',
            'transaction_date' => now()->toDateString() . ' 14:05:37',
        ]);

        $return = Transaction::where('borrow_transaction_id', $borrow->id)->firstOrFail();
        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertDontSee('Hapus tracking pengembalian')
            ->assertDontSee('deleteTransactionWarningModal')
            ->assertSee(now()->format('d-m-Y') . ' 14:05:37')
            ->assertSee('Pengembalian oleh Staf Peminjam, ke Ruang Penyimpanan, kondisi Baik')
            ->assertDontSee('Pengembalian: ' . now()->toDateString())
            ->assertDontSee('<td>Return</td>', false);

        $this->get(route('transactions.index', [
            'type' => 'return',
            'date' => now()->toDateString(),
        ]))
            ->assertOk()
            ->assertDontSee('Hapus tracking pengembalian')
            ->assertDontSee('<td>Return</td>', false);

        $this->actingAs($user)
            ->delete(route('transactions.destroy', $borrow))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Hapus tracking pengembalian')
            ->assertSee('id="deleteTransactionWarningModal"', false)
            ->assertSee('Hapus Riwayat Transaksi?')
            ->assertSee('Catatan peminjaman dan pengembalian ini akan')
            ->assertSee('Tenang, jumlah stok barang tidak akan berubah.')
            ->assertSee('Ya, Hapus Permanen');

        $this->delete(route('transactions.destroy', $borrow))
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $return->id]);
        $this->assertDatabaseMissing('transactions', ['id' => $borrow->id]);
        $this->assertSame(1, $item->fresh()->quantity);
        $this->assertSame('Tersedia', $item->fresh()->status);
    }

    public function test_damaged_return_is_recorded_and_stored_in_warehouse(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Pengembali', 'is_active' => true]);
        $warehouse = Location::ensureActive('Gudang');
        $readingRoom = Location::create(['name' => 'Ruang Baca', 'is_active' => true]);
        $item = Item::create([
            'code' => 'INV-RET-DAMAGED',
            'name' => 'Barang Dikembalikan Rusak',
            'location_id' => $readingRoom->id,
            'quantity' => 1,
            'status' => 'Dipinjam',
        ]);
        $borrow = Transaction::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'to_person' => $staff->name,
            'from_location' => $readingRoom->name,
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->actingAs($user)->post(route('transactions.return'), [
            'borrow_transaction_id' => $borrow->id,
            'from_person' => $staff->name,
            'to_location' => $readingRoom->name,
            'transaction_date' => now()->toDateString(),
            'condition' => 'Rusak',
            'notes' => '',
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame($warehouse->id, $item->fresh()->location_id);
        $this->assertSame('Rusak', $item->fresh()->condition);
        $this->assertSame('Tersedia', $item->fresh()->status);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'type' => 'return',
            'from_location' => 'Gudang',
            'to_location' => 'Gudang',
        ]);
    }
}