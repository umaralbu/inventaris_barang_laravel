<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('transactions', 'borrow_transaction_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreignId('borrow_transaction_id')
                    ->nullable()
                    ->constrained('transactions')
                    ->nullOnDelete();
            });
        }

        $matchedBorrowIds = [];
        $returns = DB::table('transactions')
            ->where('type', 'return')
            ->where('status', 'Dikembalikan')
            ->whereNull('borrow_transaction_id')
            ->orderBy('transaction_date')
            ->get();

        foreach ($returns as $return) {
            $candidates = DB::table('transactions')
                ->where('item_id', $return->item_id)
                ->where('type', 'borrow')
                ->where('status', 'Dikembalikan')
                ->where('transaction_date', '<=', $return->transaction_date)
                ->when($matchedBorrowIds, fn ($query) => $query->whereNotIn('id', $matchedBorrowIds))
                ->get(['id']);

            if ($candidates->count() === 1) {
                $borrowId = $candidates->first()->id;
                DB::table('transactions')
                    ->where('id', $return->id)
                    ->update(['borrow_transaction_id' => $borrowId]);
                $matchedBorrowIds[] = $borrowId;
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transactions', 'borrow_transaction_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('borrow_transaction_id');
            });
        }
    }
};