<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_form_can_search_items_and_shows_their_current_location(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $origin = Location::ensureActive('Ruang Penyimpanan');
        Item::create([
            'code' => 'TRANSFER-SEARCH',
            'name' => 'Kursi Lipat',
            'brand' => 'UMS',
            'location_id' => $origin->id,
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($admin)->get(route('transactions.transfer'))
            ->assertOk()
            ->assertSee('id="transferItemSearch"', false)
            ->assertSee('data-search="TRANSFER-SEARCH Kursi Lipat UMS"', false)
            ->assertSee('data-location="Ruang Penyimpanan"', false)
            ->assertSee('id="transferFromLocation"', false)
            ->assertDontSee('name="from_location"', false);
    }

    public function test_transfer_source_location_is_derived_from_the_selected_item(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $origin = Location::ensureActive('Ruang Penyimpanan');
        $destination = Location::create(['name' => 'Ruang Baca']);
        Staff::create(['name' => 'Petugas Transfer', 'is_active' => true]);
        $item = Item::create([
            'code' => 'TRANSFER-SOURCE',
            'name' => 'Kursi',
            'location_id' => $origin->id,
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($admin)->post(route('transactions.transfer'), [
            'item_id' => $item->id,
            'from_location' => 'Lokasi palsu',
            'to_location' => $destination->name,
            'transferred_by' => 'Petugas Transfer',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'type' => 'transfer',
            'from_location' => 'Ruang Penyimpanan',
            'to_location' => 'Ruang Baca',
        ]);
    }

    public function test_transfer_updates_unit_location_without_changing_availability_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $origin = Location::ensureActive('Ruang Penyimpanan');
        $destination = Location::create(['name' => 'Ruang Baca']);
        Staff::create(['name' => 'Petugas Transfer', 'is_active' => true]);
        $item = Item::create([
            'code' => 'TRANSFER-001',
            'name' => 'Kursi',
            'location_id' => $origin->id,
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($admin)->get(route('transactions.transfer'))
            ->assertOk()
            ->assertSee('id="transferItemSearch"', false)
            ->assertSee('role="listbox" aria-label="Pilih barang"', false)
            ->assertSee('data-location="Ruang Penyimpanan"', false)
            ->assertSee('id="transferFromLocation"', false)
            ->assertDontSee('name="from_location"', false);

        $this->post(route('transactions.transfer'), [
            'item_id' => $item->id,
            'from_location' => 'Lokasi palsu',
            'to_location' => $destination->name,
            'transferred_by' => 'Petugas Transfer',
            'transaction_date' => now()->toDateString(),
            'reason' => 'Penataan ulang',
        ])->assertRedirect(route('transactions.index'));

        $this->assertSame('Tersedia', $item->fresh()->status);
        $this->assertSame($destination->id, $item->fresh()->location_id);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'type' => 'transfer',
            'status' => 'Dipindahkan',
            'from_location' => $origin->name,
            'to_location' => $destination->name,
            'quantity' => 1,
        ]);
    }

    public function test_transfer_rejects_a_unit_with_an_active_loan(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $origin = Location::ensureActive('Ruang Penyimpanan');
        Location::create(['name' => 'Ruang Baca']);
        Staff::create(['name' => 'Petugas Transfer', 'is_active' => true]);
        $item = Item::create([
            'code' => 'TRANSFER-002',
            'name' => 'Kursi',
            'location_id' => $origin->id,
            'quantity' => 1,
            'status' => 'Dipinjam',
        ]);
        Transaction::create([
            'item_id' => $item->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->actingAs($admin)->from(route('transactions.transfer'))->post(route('transactions.transfer'), [
            'item_id' => $item->id,
            'to_location' => 'Ruang Baca',
            'transferred_by' => 'Petugas Transfer',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.transfer'))
            ->assertSessionHasErrors('item_id');

        $this->assertSame($origin->id, $item->fresh()->location_id);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_damaged_items_are_not_offered_or_accepted_for_transfer(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $staff = Staff::create(['name' => 'Petugas Transfer', 'is_active' => true]);
        $destination = Location::create(['name' => 'Ruang Baca', 'is_active' => true]);
        $damagedItem = Item::create([
            'code' => 'TRANSFER-DAMAGED',
            'name' => 'Barang Rusak',
            'quantity' => 1,
            'condition' => 'Rusak',
            'status' => 'Tersedia',
        ]);

        $this->actingAs($admin)->get(route('transactions.transfer'))
            ->assertOk()
            ->assertDontSee('TRANSFER-DAMAGED')
            ->assertDontSee('Barang Rusak');

        $this->post(route('transactions.transfer'), [
            'item_id' => $damagedItem->id,
            'to_location' => $destination->name,
            'transferred_by' => $staff->name,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('item_id');

        $this->assertDatabaseMissing('transactions', ['item_id' => $damagedItem->id, 'type' => 'transfer']);
    }
}
