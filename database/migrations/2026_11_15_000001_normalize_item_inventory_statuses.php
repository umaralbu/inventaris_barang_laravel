<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('items')
                ->where('status', 'Rusak')
                ->where(function ($query) {
                    $query->whereNull('condition')
                        ->orWhereNotIn('condition', ['Rusak', 'Perlu Perbaikan']);
                })
                ->update([
                    'condition' => 'Rusak',
                    'updated_at' => now(),
                ]);

            DB::table('items')
                ->whereIn('status', ['Rusak', 'Dipindahkan'])
                ->whereExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('transactions')
                        ->whereColumn('transactions.item_id', 'items.id')
                        ->where('transactions.type', 'borrow')
                        ->where('transactions.status', 'Dipinjam');
                })
                ->update([
                    'status' => 'Dipinjam',
                    'updated_at' => now(),
                ]);

            DB::table('items')
                ->whereIn('status', ['Rusak', 'Dipindahkan'])
                ->update([
                    'status' => 'Tersedia',
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        // Normalized inventory statuses cannot be safely restored to the legacy values.
    }
};
