<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowItemPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_picker_only_lists_available_items_and_restores_selection_after_validation_failure(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
<<<<<<< HEAD
        $item = Item::create(['code' => 'PICK-001', 'name' => 'Laptop Kantor', 'brand' => 'Lenovo', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-002', 'name' => 'Barang Kosong', 'quantity' => 1, 'status' => 'Dipinjam']);
=======
        $item = Item::create(['code' => 'PICK-001', 'name' => 'Laptop Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-004', 'name' => 'Laptop Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-002', 'name' => 'Barang Kosong', 'quantity' => 0, 'status' => 'Tersedia']);
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe
        Item::create(['code' => 'PICK-003', 'name' => 'Barang Nonaktif', 'quantity' => 1, 'status' => 'Tersedia', 'is_active' => false]);

        $borrowForm = $this->actingAs($user)->get(route('transactions.borrow'));
        $borrowForm->assertOk()
            ->assertSee('data-label="Laptop Kantor — Lenovo — PICK-001"', false)
            ->assertSee('data-name="Laptop Kantor" data-brand="Lenovo"', false)
            ->assertSee('data-condition="Baik"', false)
            ->assertSee('Laptop Kantor <span class="text-secondary">· Lenovo</span>', false)
            ->assertSee('Kondisi: Baik')
            ->assertDontSee('Stok:')
            ->assertSee('<th class="borrow-selected-name">Nama barang</th>', false)
            ->assertDontSee('<th class="borrow-selected-brand">Merek</th>', false)
            ->assertSee('<th class="borrow-selected-condition">Kondisi</th>', false)
            ->assertSee('<th class="borrow-selected-location">Ke lokasi</th>', false)
            ->assertSee('id="borrowItemDone"', false)
            ->assertSee('Format 24 jam (HH:mm:ss)')
            ->assertSee('Selesai memilih barang')
            ->assertDontSee('Merek: Lenovo')
            ->assertDontSee('Barang Kosong')
            ->assertDontSee('Barang Nonaktif');
        $this->assertMatchesRegularExpression(
            '/<input id="borrowTransactionTime" type="time" name="transaction_time"[^>]*value="\d{2}:\d{2}:\d{2}"[^>]*step="1"/',
            $borrowForm->getContent()
        );

        $this->from(route('transactions.borrow'))->post(route('transactions.borrow'), ['item_ids' => [$item->id]])
            ->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('staff_id')
            ->assertSessionHasInput('item_ids', [$item->id]);

        $this->get(route('transactions.borrow'))->assertOk()
            ->assertSee('name="item_ids[]"', false)
            ->assertSee('data-search="PICK-001 Laptop Kantor Lenovo" selected', false);

        $this->post(route('transactions.borrow'), ['item_ids' => ['Laptop Kantor']])
            ->assertSessionHasErrors('item_ids.0');
    }

    public function test_staff_can_submit_multiple_available_items_in_one_borrow_request(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $item = Item::create(['code' => 'PICK-004', 'name' => 'Kamera', 'quantity' => 1, 'unit' => 'unit', 'status' => 'Tersedia']);
        $secondItem = Item::create(['code' => 'PICK-005', 'name' => 'Tripod', 'quantity' => 1, 'unit' => 'unit', 'status' => 'Tersedia']);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $meetingRoom = Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $readingRoom = Location::create(['name' => 'Ruang Baca', 'is_active' => true]);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
<<<<<<< HEAD
            ->assertSee('data-stock="1" data-unit="unit"', false)
            ->assertSee('name="item_ids[]"', false)
            ->assertSee('aria-multiselectable="true"', false)
            ->assertSee('id="borrowItemSelectedRows"', false)
            ->assertSee('Daftar barang yang diambil')
            ->assertSee('Ke lokasi')
            ->assertSee('Keperluan');
=======
            ->assertSee('data-label="Laptop Kantor — PICK-001"', false)
            ->assertSee('data-name="Laptop Kantor" data-code="PICK-004" data-stock="1"', false)
            ->assertDontSee('Barang Kosong')
            ->assertDontSee('Barang Nonaktif');
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe

        $this->from(route('transactions.borrow'))->post(route('transactions.borrow'), [
            'item_ids' => [$item->id, $secondItem->id],
            'staff_id' => $staff->id,
            'item_locations' => [$item->id => $meetingRoom->name],
            'transaction_date' => now()->toDateString(),
            'item_due_datetimes' => [
                $item->id => now()->addDay()->format('Y-m-d\TH:i'),
                $secondItem->id => now()->addDay()->format('Y-m-d\TH:i'),
            ],
        ])->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('item_locations');
        $this->assertDatabaseCount('transactions', 0);

<<<<<<< HEAD
        $this->from(route('transactions.borrow'))->post(route('transactions.borrow'), [
            'item_ids' => [$item->id, $secondItem->id],
            'staff_id' => $staff->id,
            'item_locations' => [
                $item->id => $meetingRoom->name,
                $secondItem->id => $readingRoom->name,
            ],
            'item_purposes' => [
                $item->id => 'Presentasi',
                $secondItem->id => 'Dokumentasi acara',
            ],
            'transaction_date' => now()->toDateString(),
            'item_due_datetimes' => [
                $item->id => now()->addDay()->format('Y-m-d\TH:i'),
                $secondItem->id => now()->addDays(2)->format('Y-m-d\TH:i'),
            ],
        ])->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success', 'Permintaan pinjaman untuk 2 unit barang berhasil dikirim dan menunggu persetujuan admin.');
=======
        $this->get(route('transactions.borrow'))->assertOk()
            ->assertSee('data-code="PICK-001" data-stock="1" data-unit="pcs" selected', false);
>>>>>>> e25ccdfd4d6d1172fe50c618a8832ff52a1632fe

        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'status' => 'Menunggu Persetujuan',
            'quantity' => 1,
            'to_location' => 'Ruang Rapat',
            'notes' => 'Presentasi',
            'due_date' => now()->addDay()->toDateString() . ' 00:00:00',
        ]);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $secondItem->id,
            'status' => 'Menunggu Persetujuan',
            'quantity' => 1,
            'to_location' => 'Ruang Baca',
            'notes' => 'Dokumentasi acara',
            'due_date' => now()->addDays(2)->toDateString() . ' 00:00:00',
        ]);
    }

    public function test_pending_items_are_not_offered_or_borrowed_again(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $availableItem = Item::create(['code' => 'PICK-006', 'name' => 'Mikrofon', 'quantity' => 1, 'status' => 'Tersedia']);
        $pendingItem = Item::create(['code' => 'PICK-007', 'name' => 'Speaker', 'quantity' => 1, 'status' => 'Tersedia']);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        Transaction::create([
            'item_id' => $pendingItem->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Menunggu Persetujuan',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
            ->assertSee('Mikrofon')
            ->assertDontSee('Speaker');

        $this->post(route('transactions.borrow'), [
            'item_ids' => [$availableItem->id, $pendingItem->id],
            'staff_id' => $staff->id,
            'item_locations' => [
                $availableItem->id => 'Ruang Rapat',
                $pendingItem->id => 'Ruang Rapat',
            ],
            'transaction_date' => now()->toDateString(),
            'item_due_datetimes' => [
                $availableItem->id => now()->addDay()->format('Y-m-d\TH:i'),
                $pendingItem->id => now()->addDay()->format('Y-m-d\TH:i'),
            ],
        ])->assertSessionHasErrors('item_ids');

        $this->assertDatabaseMissing('transactions', [
            'item_id' => $availableItem->id,
            'status' => 'Menunggu Persetujuan',
        ]);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_damaged_items_are_not_offered_or_accepted_for_borrowing(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $location = Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $damagedItem = Item::create([
            'code' => 'PICK-DAMAGED',
            'name' => 'Barang Rusak',
            'quantity' => 1,
            'status' => 'Tersedia',
            'condition' => 'Rusak',
        ]);
        \Illuminate\Support\Facades\DB::table('items')
            ->where('id', $damagedItem->id)
            ->update(['status' => 'Tersedia']);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
            ->assertDontSee('Barang Rusak')
            ->assertDontSee('PICK-DAMAGED');

        $this->actingAs($user)->post(route('transactions.borrow'), [
            'item_ids' => [$damagedItem->id],
            'staff_id' => $staff->id,
            'item_locations' => [$damagedItem->id => $location->name],
            'transaction_date' => now()->toDateString(),
            'item_due_datetimes' => [$damagedItem->id => now()->addDay()->format('Y-m-d\TH:i')],
        ])->assertSessionHasErrors('item_ids');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_undamaged_items_cannot_be_assigned_to_the_damaged_warehouse_during_borrowing(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $warehouse = Location::ensureActive('Gudang');
        $item = Item::create([
            'code' => 'PICK-WAREHOUSE-DESTINATION',
            'name' => 'Barang Baik',
            'quantity' => 1,
            'status' => 'Tersedia',
            'condition' => 'Baik',
        ]);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
            ->assertDontSee('<option value="Gudang">Gudang</option>', false);

        $this->actingAs($user)->post(route('transactions.borrow'), [
            'item_ids' => [$item->id],
            'staff_id' => $staff->id,
            'item_locations' => [$item->id => $warehouse->name],
            'transaction_date' => now()->toDateString(),
            'item_due_datetimes' => [$item->id => now()->addDay()->format('Y-m-d\TH:i')],
        ])->assertSessionHasErrors("item_locations.{$item->id}");

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_borrow_quantity_is_distributed_across_available_codes_with_the_same_name(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $firstItem = Item::create(['code' => 'PICK-SAME-001', 'name' => 'Kursi Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        $secondItem = Item::create(['code' => 'PICK-SAME-002', 'name' => 'Kursi Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-DIFFERENT-001', 'name' => 'Meja Kantor', 'quantity' => 1, 'status' => 'Tersedia']);

        $this->actingAs($user)->post(route('transactions.borrow'), [
            'item_id' => $firstItem->id,
            'staff_id' => $staff->id,
            'to_location' => 'Ruang Rapat',
            'transaction_date' => now()->toDateString(),
            'quantity' => 2,
            'purpose' => 'Kegiatan kantor',
        ])->assertRedirect(route('transactions.index'));

        $borrows = Transaction::where('type', 'borrow')->orderBy('item_id')->get();
        $this->assertCount(2, $borrows);
        $this->assertSame([$firstItem->id, $secondItem->id], $borrows->pluck('item_id')->all());
        $this->assertSame([1, 1], $borrows->pluck('quantity')->all());
        $this->assertSame(['Menunggu Persetujuan', 'Menunggu Persetujuan'], $borrows->pluck('status')->all());
    }

    public function test_borrow_quantity_cannot_use_stock_from_a_different_item_name(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $item = Item::create(['code' => 'PICK-ONLY-001', 'name' => 'Kursi Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-OTHER-001', 'name' => 'Meja Kantor', 'quantity' => 1, 'status' => 'Tersedia']);

        $this->actingAs($user)->from(route('transactions.borrow'))->post(route('transactions.borrow'), [
            'item_id' => $item->id,
            'staff_id' => $staff->id,
            'to_location' => 'Ruang Rapat',
            'transaction_date' => now()->toDateString(),
            'quantity' => 2,
        ])->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_selected_code_count_must_match_requested_quantity(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $item = Item::create(['code' => 'PICK-COUNT-001', 'name' => 'Lemari Arsip', 'quantity' => 1, 'status' => 'Tersedia']);

        $this->actingAs($user)->from(route('transactions.borrow'))->post(route('transactions.borrow'), [
            'item_id' => $item->id,
            'item_ids' => [$item->id],
            'staff_id' => $staff->id,
            'to_location' => 'Ruang Rapat',
            'transaction_date' => now()->toDateString(),
            'quantity' => 2,
        ])->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_selected_codes_create_one_borrow_record_per_code(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $firstItem = Item::create(['code' => 'PICK-CHOSEN-001', 'name' => 'Monitor', 'quantity' => 1, 'status' => 'Tersedia']);
        $secondItem = Item::create(['code' => 'PICK-CHOSEN-002', 'name' => 'Monitor', 'quantity' => 1, 'status' => 'Tersedia']);

        $this->actingAs($user)->post(route('transactions.borrow'), [
            'item_id' => $firstItem->id,
            'item_ids' => [$firstItem->id, $secondItem->id],
            'staff_id' => $staff->id,
            'to_location' => 'Ruang Rapat',
            'transaction_date' => now()->toDateString(),
            'quantity' => 2,
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', ['item_id' => $firstItem->id, 'type' => 'borrow', 'quantity' => 1]);
        $this->assertDatabaseHas('transactions', ['item_id' => $secondItem->id, 'type' => 'borrow', 'quantity' => 1]);
    }
}
