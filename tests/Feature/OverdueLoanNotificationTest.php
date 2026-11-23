<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverdueLoanNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_each_overdue_loan_on_dashboard_and_transactions_page(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-12 10:00:00', 'Asia/Jakarta'));
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $overdueByDate = $this->createBorrow($admin, 'LATE-DATE', 'Dipinjam', now()->subDay()->toDateString());
        $overdueByTime = $this->createBorrow($admin, 'LATE-TIME', 'Dipinjam', now()->toDateString(), '09:59:00');
        $notYetDue = $this->createBorrow($admin, 'NOT-LATE', 'Dipinjam', now()->toDateString(), '10:01:00');
        $pending = $this->createBorrow($admin, 'PENDING', 'Menunggu Persetujuan', now()->subDay()->toDateString());
        $returned = $this->createBorrow($admin, 'RETURNED', 'Dikembalikan', now()->subDay()->toDateString());
        $expectedIds = [$overdueByDate->id, $overdueByTime->id];

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="overdue-notice"', false)
            ->assertSee('LATE-DATE')
            ->assertSee('LATE-TIME')
            ->assertViewHas('overdueTransactions', fn ($transactions) => $transactions->pluck('id')->sort()->values()->all() === $expectedIds);

        $this->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('id="overdue-notice"', false)
            ->assertSee('LATE-DATE')
            ->assertSee('LATE-TIME')
            ->assertViewHas('overdueTransactions', fn ($transactions) => $transactions->pluck('id')->sort()->values()->all() === $expectedIds);
    }

    public function test_user_dashboard_only_shows_their_overdue_loans(): void
    {
        $this->travelTo(\Carbon\Carbon::parse('2026-11-12 10:00:00', 'Asia/Jakarta'));
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);
        $myOverdue = $this->createBorrow($user, 'MY-LATE', 'Dipinjam', now()->subDay()->toDateString());
        $this->createBorrow($otherUser, 'OTHER-LATE', 'Dipinjam', now()->subDay()->toDateString());

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="overdue-notice"', false)
            ->assertSee('MY-LATE')
            ->assertDontSee('OTHER-LATE')
            ->assertViewHas('overdueTransactions', fn ($transactions) => $transactions->pluck('id')->all() === [$myOverdue->id]);
    }

    private function createBorrow(User $user, string $code, string $status, string $dueDate, ?string $dueTime = null): Transaction
    {
        $item = Item::create([
            'code' => $code,
            'name' => 'Barang ' . $code,
            'quantity' => 1,
            'status' => $status === 'Dipinjam' ? 'Dipinjam' : 'Tersedia',
        ]);

        return Transaction::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => $status,
            'to_person' => 'Staf Peminjam',
            'quantity' => 1,
            'transaction_date' => now()->subDays(2),
            'due_date' => $dueDate,
            'due_time' => $dueTime,
        ]);
    }
}
