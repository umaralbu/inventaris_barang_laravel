<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryUnitConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_rejects_an_item_row_representing_multiple_units(): void
    {
        $this->expectException(QueryException::class);

        Item::create([
            'code' => 'UNIT-MULTI-1',
            'name' => 'Pensil',
            'quantity' => 2,
            'status' => 'Tersedia',
        ]);
    }

    public function test_database_rejects_a_transaction_row_covering_multiple_units(): void
    {
        $item = Item::create([
            'code' => 'UNIT-TRANSACTION-1',
            'name' => 'Pensil',
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);

        $this->expectException(QueryException::class);

        Transaction::create([
            'item_id' => $item->id,
            'type' => 'borrow',
            'status' => 'Menunggu Persetujuan',
            'quantity' => 2,
            'transaction_date' => now(),
        ]);
    }

    public function test_database_rejects_deleting_a_unit_with_transaction_history(): void
    {
        $item = Item::create([
            'code' => 'UNIT-HISTORY-1',
            'name' => 'Pensil',
            'quantity' => 1,
            'status' => 'Tersedia',
        ]);
        Transaction::create([
            'item_id' => $item->id,
            'type' => 'borrow',
            'status' => 'Dikembalikan',
            'quantity' => 1,
            'transaction_date' => now(),
        ]);

        $this->expectException(QueryException::class);

        $item->delete();
    }
}
