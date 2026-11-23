<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Location;
use App\Models\Staff;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanDurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrow_form_shows_due_date_input_in_the_selected_items_table(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('transactions.borrow'))
            ->assertOk()
            ->assertSee('<th class="borrow-selected-due-date">Tanggal jatuh tempo</th>', false)
            ->assertSee('<th class="borrow-selected-remove">Hapus</th>', false)
            ->assertSee('item_due_datetimes[${option.dataset.value}]')
            ->assertDontSee('name="due_datetime"', false)
            ->assertDontSee('name="loan_duration_days"', false);
    }

    public function test_borrow_request_saves_due_date_and_time_and_shows_them_in_details(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $location = Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $item = Item::create([
            'code' => 'LOAN-001',
            'name' => 'Proyektor',
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);
        $borrowDate = now()->toDateString();
        $dueDateTime = $borrowDate . 'T11:52';

        $this->actingAs($user)->post(route('transactions.borrow'), [
            'item_ids' => [$item->id],
            'staff_id' => $staff->id,
            'item_locations' => [$item->id => $location->name],
            'transaction_date' => $borrowDate,
            'transaction_time' => '09:30',
            'item_due_datetimes' => [$item->id => $dueDateTime],
        ])->assertRedirect(route('transactions.index'));

        $transaction = Transaction::where('item_id', $item->id)->firstOrFail();
        $this->assertNull($transaction->loan_duration_days);
        $this->assertSame($borrowDate, $transaction->due_date->toDateString());
        $this->assertSame('11:52:00', $transaction->due_time);

        $this->actingAs($user)
            ->get(route('transactions.show', $transaction))
            ->assertOk()
            ->assertDontSee('Durasi Peminjaman')
            ->assertSee(\Carbon\Carbon::parse($borrowDate)->format('d-m-Y') . ' 11:52');
        $this->get(route('transactions.index'))
            ->assertOk()
            ->assertDontSee('<th>Durasi</th>', false)
            ->assertSee('<th>Jatuh Tempo</th>', false)
            ->assertSee($transaction->formatted_due_date);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)->post(route('transactions.approve', $transaction))->assertRedirect(route('transactions.index'));

        $this->get(route('transactions.return'))
            ->assertOk()
            ->assertSee('jatuh tempo: ' . $transaction->formatted_due_date);
    }

    public function test_borrow_due_datetime_must_be_after_the_transaction_datetime(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $staff = Staff::create(['name' => 'Staf Peminjam', 'is_active' => true]);
        $location = Location::create(['name' => 'Ruang Rapat', 'is_active' => true]);
        $item = Item::create([
            'code' => 'LOAN-002',
            'name' => 'Kamera',
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $this->actingAs($user)->from(route('transactions.borrow'))->post(route('transactions.borrow'), [
            'item_ids' => [$item->id],
            'staff_id' => $staff->id,
            'item_locations' => [$item->id => $location->name],
            'transaction_date' => now()->toDateString(),
            'transaction_time' => '09:30',
            'item_due_datetimes' => [$item->id => now()->format('Y-m-d') . 'T09:29'],
        ])->assertRedirect(route('transactions.borrow'))
            ->assertSessionHasErrors("item_due_datetimes.{$item->id}");

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_overdue_status_uses_due_time_when_available(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-12 10:00:00', 'Asia/Jakarta'));
        $item = Item::create([
            'code' => 'LOAN-003',
            'name' => 'Speaker',
            'quantity' => 1,
            'status' => 'Dipinjam',
        ]);
        $transaction = Transaction::create([
            'item_id' => $item->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'transaction_date' => now(),
            'due_date' => now()->toDateString(),
            'due_time' => '10:01:00',
        ]);

        $this->assertFalse($transaction->is_overdue);

        $transaction->update(['due_time' => '09:59:00']);
        $this->assertTrue($transaction->fresh()->is_overdue);
    }
}
