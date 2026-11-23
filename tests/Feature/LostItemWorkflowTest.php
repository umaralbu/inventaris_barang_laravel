<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LostItemWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_can_report_an_active_borrow_as_lost_without_recording_a_return(): void
    {
        [$user, $item, $borrow] = $this->createActiveBorrow();
        $borrow->update(['transaction_date' => now()->subDay()]);

        $this->actingAs($user)
            ->post(route('transactions.report-lost', $borrow), [
                'notes' => 'Barang tertinggal saat kegiatan luar.',
            ])
            ->assertRedirect(route('transactions.show', $borrow))
            ->assertSessionHas('success');

        $report = Transaction::where('type', 'loss_report')->firstOrFail();
        $this->assertSame('Menunggu Konfirmasi', $report->status);
        $this->assertSame($borrow->id, $report->borrow_transaction_id);
        $this->assertSame('Dipinjam', $borrow->fresh()->status);
        $this->assertSame('Dipinjam', $item->fresh()->status);
        $this->assertDatabaseMissing('transactions', [
            'borrow_transaction_id' => $borrow->id,
            'type' => 'return',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.show', $borrow))
            ->assertOk()
            ->assertSee('Laporan Kehilangan')
            ->assertSee('Barang tertinggal saat kegiatan luar.');

        $this->actingAs($user)
            ->get(route('transactions.return'))
            ->assertOk()
            ->assertDontSee($item->code);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Konfirmasi Hilang')
            ->assertSee('Tolak Laporan');

        $this->get(route('transactions.index', ['type' => 'loss', 'date' => now()->toDateString()]))
            ->assertOk()
            ->assertSee($item->code)
            ->assertSee('Laporan hilang menunggu konfirmasi');
    }

    public function test_admin_confirmation_closes_the_loan_as_lost_and_preserves_physical_condition(): void
    {
        [$user, $item, $borrow] = $this->createActiveBorrow();
        $report = $this->createLossReport($user, $item, $borrow);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('transactions.confirm-lost', $report))
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success');

        $this->assertSame('Hilang', $borrow->fresh()->status);
        $this->assertSame('Hilang', $item->fresh()->status);
        $this->assertSame('Baik', $item->fresh()->condition);
        $this->assertSame('Dikonfirmasi', $report->fresh()->status);
        $this->assertDatabaseMissing('transactions', [
            'borrow_transaction_id' => $borrow->id,
            'type' => 'return',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('barangDipinjam', 0)
            ->assertViewHas('itemsByStatus', fn ($itemsByStatus) => $itemsByStatus['lost']->contains(
                fn ($lostItem) => $lostItem->name === $item->name && (int) $lostItem->quantity === 1
            ));

        $this->actingAs($user)
            ->get(route('transactions.return'))
            ->assertOk()
            ->assertDontSee($item->code);
    }

    public function test_admin_can_reject_a_loss_report_and_keep_the_borrow_active(): void
    {
        [$user, $item, $borrow] = $this->createActiveBorrow();
        $report = $this->createLossReport($user, $item, $borrow);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('transactions.reject-lost', $report))
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success');

        $this->assertSame('Ditolak', $report->fresh()->status);
        $this->assertSame('Dipinjam', $borrow->fresh()->status);
        $this->assertSame('Dipinjam', $item->fresh()->status);

        $this->actingAs($user)
            ->get(route('transactions.return'))
            ->assertOk()
            ->assertSee($item->code);
    }

    public function test_another_user_cannot_report_someone_elses_borrow_as_lost(): void
    {
        [$owner, $item, $borrow] = $this->createActiveBorrow();
        $otherUser = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($otherUser)
            ->post(route('transactions.report-lost', $borrow), [
                'notes' => 'Tidak berwenang',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('transactions', [
            'item_id' => $item->id,
            'type' => 'loss_report',
        ]);
    }

    public function test_admin_cannot_report_a_borrow_as_lost(): void
    {
        [$user, $item, $borrow] = $this->createActiveBorrow();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('transactions.show', $borrow))
            ->assertOk()
            ->assertDontSee('Laporkan Hilang')
            ->assertDontSee('Barang tidak ditemukan?');

        $this->post(route('transactions.report-lost', $borrow), [
            'notes' => 'Tidak berwenang.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('transactions', [
            'item_id' => $item->id,
            'type' => 'loss_report',
        ]);
    }

    public function test_item_crud_requires_loss_report_for_items_with_an_active_borrow_and_allows_recovery_update(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        [$user, $item, $borrow] = $this->createActiveBorrow();

        $this->actingAs($admin)
            ->put(route('items.update', $item), [
                'name' => $item->name,
                'quantity' => 1,
                'unit' => $item->unit,
                'condition' => 'Baik',
                'status' => 'Hilang',
            ])
            ->assertSessionHasErrors('status');

        $report = $this->createLossReport($user, $item, $borrow);
        $this->actingAs($admin)
            ->post(route('transactions.confirm-lost', $report))
            ->assertRedirect(route('transactions.index'));

        $this->put(route('items.update', $item->fresh()), [
            'name' => $item->name,
            'quantity' => 1,
            'unit' => $item->unit,
            'condition' => 'Baik',
            'status' => 'Tersedia',
        ])
            ->assertRedirect(route('items.index'))
            ->assertSessionHas('success', 'Barang diperbarui. Pastikan kondisi fisik dan statusnya sudah sesuai setelah ditemukan.');

        $this->assertSame('Tersedia', $item->fresh()->status);
        $this->assertSame('Baik', $item->fresh()->condition);
        $this->assertSame('Ditemukan', $borrow->fresh()->status);
        $this->assertDatabaseHas('transactions', [
            'item_id' => $item->id,
            'user_id' => $admin->id,
            'type' => 'recovery',
            'status' => 'Ditemukan',
            'condition' => 'Baik',
        ]);

        $this->get(route('transactions.index', ['type' => 'found']))
            ->assertOk()
            ->assertSee($item->code)
            ->assertSee('Ditemukan');
    }

    public function test_migration_syncs_the_latest_lost_borrow_when_its_item_is_already_available(): void
    {
        [$user, $item, $borrow] = $this->createActiveBorrow();
        $borrow->update(['status' => 'Hilang']);
        DB::table('items')->where('id', $item->id)->update(['status' => 'Tersedia']);

        $migration = include database_path('migrations/2026_11_12_000003_sync_recovered_borrow_status.php');
        $migration->up();

        $this->assertSame('Ditemukan', $borrow->fresh()->status);
    }

    private function createActiveBorrow(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $item = Item::create([
            'code' => 'LOST-WORKFLOW-001',
            'name' => 'Laptop Uji',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Dipinjam',
            'condition' => 'Baik',
        ]);
        $borrow = Transaction::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => 'borrow',
            'status' => 'Dipinjam',
            'quantity' => 1,
            'transaction_date' => now(),
            'to_person' => 'Staf Pengguna',
            'condition' => 'Baik',
        ]);

        return [$user, $item, $borrow];
    }

    private function createLossReport(User $user, Item $item, Transaction $borrow): Transaction
    {
        return Transaction::create([
            'item_id' => $item->id,
            'borrow_transaction_id' => $borrow->id,
            'user_id' => $user->id,
            'type' => 'loss_report',
            'status' => 'Menunggu Konfirmasi',
            'quantity' => 1,
            'transaction_date' => now(),
            'from_person' => $user->name,
            'notes' => 'Alasan kehilangan.',
        ]);
    }
}
