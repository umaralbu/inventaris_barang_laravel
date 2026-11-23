<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_analytics_with_inventory_value_and_monthly_borrow_counts(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(12, 0));
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $availableItem = Item::create([
            'code' => 'ANALYTICS-AVAILABLE',
            'name' => 'Barang Tersedia',
            'quantity' => 1,
            'purchase_price' => 120000,
            'status' => 'Tersedia',
            'is_active' => true,
        ]);
        $borrowedItem = Item::create([
            'code' => 'ANALYTICS-BORROWED',
            'name' => 'Barang Dipinjam',
            'quantity' => 1,
            'purchase_price' => 250000,
            'status' => 'Dipinjam',
            'is_active' => true,
        ]);
        Item::create([
            'code' => 'ANALYTICS-INACTIVE',
            'name' => 'Barang Nonaktif',
            'quantity' => 1,
            'purchase_price' => 900000,
            'is_active' => false,
        ]);

        $this->createBorrow($borrowedItem, $user, 'Dipinjam', '2026-11-10');
        $this->createBorrow($availableItem, $user, 'Dikembalikan', '2026-10-10');
        $this->createBorrow($availableItem, $user, 'Dikembalikan', '2026-11-15');
        $this->createBorrow($availableItem, $user, 'Menunggu Persetujuan', '2026-11-12');
        $this->createBorrow($availableItem, $user, 'Ditolak', '2026-11-13');
        $this->createBorrow($availableItem, $user, 'Dikembalikan', '2026-04-10');

        $this->actingAs($admin)
            ->get(route('admin.analytics', ['months' => 6]))
            ->assertOk()
            ->assertSee('Total Nilai Inventaris')
            ->assertSee('Rp 370.000')
            ->assertViewHas('monthlyBorrowings', function ($monthlyBorrowings): bool {
                return $monthlyBorrowings->count() === 6
                    && $monthlyBorrowings->first() === ['label' => '06/2026', 'total' => 0]
                    && $monthlyBorrowings->contains(['label' => '10/2026', 'total' => 1])
                    && $monthlyBorrowings->contains(['label' => '11/2026', 'total' => 2]);
            })
            ->assertViewHas('periodBorrowingCount', 3)
            ->assertViewHas('activeBorrowCount', 1)
            ->assertViewHas('availableItemCount', 1)
            ->assertViewHas('topBorrowedItems', fn ($items) => $items->count() === 2
                && $items->first()->name === 'Barang Tersedia'
                && (int) $items->first()->total === 2);

        $this->get(route('admin.analytics', ['months' => 12]))
            ->assertOk()
            ->assertViewHas('monthlyBorrowings', fn ($monthlyBorrowings) => $monthlyBorrowings->count() === 12
                && $monthlyBorrowings->contains(['label' => '04/2026', 'total' => 1]));
    }

    public function test_only_admins_can_open_analytics_and_dashboard_link_is_admin_only(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('admin.analytics'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('admin.analytics'));

        $this->get(route('admin.analytics'))->assertForbidden();
    }

    public function test_empty_borrowing_period_shows_an_empty_state(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.analytics'))
            ->assertOk()
            ->assertSee('Belum ada transaksi peminjaman pada periode ini.')
            ->assertSee('Belum ada data peminjaman pada periode ini.')
            ->assertViewHas('periodBorrowingCount', 0)
            ->assertViewHas('topBorrowedItems', fn ($items) => $items->isEmpty());
    }

    public function test_analytics_ranks_at_most_five_items_by_borrowing_count(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 23)->setTime(12, 0));
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        for ($rank = 1; $rank <= 6; $rank++) {
            $item = Item::create([
                'code' => 'ANALYTICS-RANK-' . $rank,
                'name' => 'Barang Peringkat ' . $rank,
                'quantity' => 1,
                'is_active' => true,
            ]);

            for ($count = 0; $count < $rank; $count++) {
                $this->createBorrow($item, $user, 'Dikembalikan', '2026-11-15');
            }
        }

        $this->actingAs($admin)
            ->get(route('admin.analytics'))
            ->assertOk()
            ->assertViewHas('topBorrowedItems', function ($items): bool {
                return $items->count() === 5
                    && $items->first()->name === 'Barang Peringkat 6'
                    && (int) $items->first()->total === 6
                    && $items->last()->name === 'Barang Peringkat 2'
                    && !$items->contains(fn ($item) => $item->name === 'Barang Peringkat 1');
            });
    }

    private function createBorrow(Item $item, User $user, string $status, string $date): void
    {
        Transaction::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => $status,
            'quantity' => 1,
            'transaction_date' => $date,
        ]);
    }
}
