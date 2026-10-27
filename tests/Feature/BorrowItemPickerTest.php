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
        $item = Item::create(['code' => 'PICK-001', 'name' => 'Laptop Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-004', 'name' => 'Laptop Kantor', 'quantity' => 1, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-002', 'name' => 'Barang Kosong', 'quantity' => 0, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-003', 'name' => 'Barang Nonaktif', 'quantity' => 1, 'status' => 'Tersedia', 'is_active' => false]);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
            ->assertSee('data-label="Laptop Kantor — PICK-001"', false)
            ->assertSee('data-name="Laptop Kantor" data-code="PICK-004" data-stock="1"', false)
            ->assertDontSee('Barang Kosong')
            ->assertDontSee('Barang Nonaktif');

        $this->from(route('transactions.borrow'))->post(route('transactions.borrow'), ['item_id' => $item->id])
            ->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('staff_id')
            ->assertSessionHasInput('item_id', $item->id);

        $this->get(route('transactions.borrow'))->assertOk()
            ->assertSee('data-code="PICK-001" data-stock="1" data-unit="pcs" selected', false);

        $this->post(route('transactions.borrow'), ['item_id' => 'Laptop Kantor'])
            ->assertSessionHasErrors('item_id');
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
