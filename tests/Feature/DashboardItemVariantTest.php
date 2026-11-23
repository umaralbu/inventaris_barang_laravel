<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardItemVariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_does_not_show_low_stock_warning(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Peringatan Stok Menipis')
            ->assertViewMissing('lowStockItems');
    }

    public function test_available_dashboard_details_group_identical_item_names_into_unit_totals(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach (['Zebra', 'Honeywell', 'Datalogic'] as $brand) {
            Item::create([
                'code' => 'SCANNER-' . $brand,
                'name' => 'Barcode Scanner',
                'brand' => $brand,
                'quantity' => 1,
                'unit' => 'Unit',
                'status' => 'Tersedia',
                'is_active' => true,
            ]);
        }

        Item::create([
            'code' => 'PRINTER-001',
            'name' => 'Printer',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('itemsByStatus', function ($itemsByStatus): bool {
                $availableItems = $itemsByStatus['available'];

                return $availableItems->count() === 2
                    && $availableItems->contains(fn ($item) => $item->name === 'Barcode Scanner'
                        && (int) $item->quantity === 3
                        && $item->unit === 'Unit');
            });
    }

    public function test_total_dashboard_details_group_identical_item_names_into_unit_totals(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach (['CALC-001', 'CALC-002'] as $code) {
            Item::create([
                'code' => $code,
                'name' => 'Kalkulator',
                'quantity' => 1,
                'unit' => 'Unit',
                'is_active' => true,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('itemsByStatus', function ($itemsByStatus): bool {
                $totalItems = $itemsByStatus['total'];

                return $totalItems->count() === 1
                    && $totalItems->first()->name === 'Kalkulator'
                    && (int) $totalItems->first()->quantity === 2
                    && $totalItems->first()->unit === 'Unit';
            });
    }

    public function test_dashboard_counts_all_damaged_items_by_condition(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Item::create([
            'code' => 'DAMAGED-001',
            'name' => 'Barang Rusak A',
            'quantity' => 1,
            'condition' => 'Rusak',
        ]);

        $secondDamagedItem = Item::create([
            'code' => 'DAMAGED-002',
            'name' => 'Barang Rusak B',
            'quantity' => 1,
            'condition' => 'Rusak',
        ]);

        DB::table('items')->where('id', $secondDamagedItem->id)->update(['status' => 'Tersedia']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('barangRusak', 2)
            ->assertViewHas('itemsByStatus', function ($itemsByStatus): bool {
                $damagedItems = $itemsByStatus['damaged'];

                return $damagedItems->count() === 2
                    && $damagedItems->contains(fn ($item) => $item->name === 'Barang Rusak A' && (int) $item->quantity === 1)
                    && $damagedItems->contains(fn ($item) => $item->name === 'Barang Rusak B' && (int) $item->quantity === 1);
            });
    }

    public function test_staff_dashboard_cards_include_available_and_personal_borrow_item_lists(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_USER]);

        foreach (['CALC-STAFF-001', 'CALC-STAFF-002'] as $code) {
            Item::create([
                'code' => $code,
                'name' => 'Kalkulator',
                'quantity' => 1,
                'unit' => 'pcs',
                'status' => 'Tersedia',
            ]);
        }

        $activeLaptop = Item::create([
            'code' => 'LAPTOP-ACTIVE',
            'name' => 'Laptop',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Dipinjam',
        ]);
        $returnedLaptop = Item::create([
            'code' => 'LAPTOP-RETURNED',
            'name' => 'Laptop',
            'quantity' => 1,
            'unit' => 'Unit',
            'status' => 'Tersedia',
        ]);

        foreach ([
            [$activeLaptop, 'Dipinjam'],
            [$returnedLaptop, 'Dikembalikan'],
        ] as [$item, $status]) {
            Transaction::create([
                'item_id' => $item->id,
                'user_id' => $staff->id,
                'type' => 'borrow',
                'status' => $status,
                'quantity' => 1,
                'transaction_date' => now(),
            ]);
        }

        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Pinjam Barang')
            ->assertSee('data-stat="available"', false)
            ->assertSee('data-stat="my-borrowed"', false)
            ->assertSee('data-stat="active-borrow"', false)
            ->assertViewHas('itemsByStatus', function ($itemsByStatus): bool {
                return $itemsByStatus['available']->contains(fn ($item) => $item->name === 'Kalkulator'
                    && (int) $item->quantity === 2);
            })
            ->assertViewHas('myBorrowedItems', fn ($items) => $items->count() === 1
                && $items->first()->name === 'Laptop'
                && (int) $items->first()->quantity === 2)
            ->assertViewHas('myActiveBorrowItems', fn ($items) => $items->count() === 1
                && $items->first()->name === 'Laptop'
                && (int) $items->first()->quantity === 1);
    }
}
