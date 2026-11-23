<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $items = DB::table('items')
            ->where('status', '!=', 'Hilang')
            ->orderBy('id')
            ->get(['id']);

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $latestBorrow = DB::table('transactions')
                    ->where('item_id', $item->id)
                    ->where('type', 'borrow')
                    ->orderByDesc('transaction_date')
                    ->orderByDesc('id')
                    ->first(['id', 'status']);

                if ($latestBorrow && $latestBorrow->status === 'Hilang') {
                    DB::table('transactions')
                        ->where('id', $latestBorrow->id)
                        ->update([
                            'status' => 'Ditemukan',
                            'updated_at' => now(),
                        ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Historical status reconciliation is intentionally irreversible.
    }
};
