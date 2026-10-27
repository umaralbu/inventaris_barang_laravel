<?php

namespace Tests\Feature;

use App\Models\Item;
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
        Item::create(['code' => 'PICK-002', 'name' => 'Barang Kosong', 'quantity' => 0, 'status' => 'Tersedia']);
        Item::create(['code' => 'PICK-003', 'name' => 'Barang Nonaktif', 'quantity' => 1, 'status' => 'Tersedia', 'is_active' => false]);

        $this->actingAs($user)->get(route('transactions.borrow'))
            ->assertOk()
            ->assertSee('data-label="Laptop Kantor — PICK-001"', false)
            ->assertDontSee('Barang Kosong')
            ->assertDontSee('Barang Nonaktif');

        $this->from(route('transactions.borrow'))->post(route('transactions.borrow'), ['item_id' => $item->id])
            ->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors('staff_id')
            ->assertSessionHasInput('item_id', $item->id);

        $this->get(route('transactions.borrow'))->assertOk()
            ->assertSee('data-search="PICK-001 Laptop Kantor" selected', false);

        $this->post(route('transactions.borrow'), ['item_id' => 'Laptop Kantor'])
            ->assertSessionHasErrors('item_id');
    }
}
