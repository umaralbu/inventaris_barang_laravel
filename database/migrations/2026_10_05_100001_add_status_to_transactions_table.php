<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            return;
        }

        if (!Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('status')->default('Dipinjam')->after('type');
            });
        }

        DB::table('transactions')
            ->whereNull('status')
            ->update([
                'status' => DB::raw("CASE 
                    WHEN type = 'borrow' THEN 'Dipinjam'
                    WHEN type = 'return' THEN 'Dikembalikan'
                    WHEN type = 'transfer' THEN 'Dipindahkan'
                    ELSE 'Aktif'
                END"),
            ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
