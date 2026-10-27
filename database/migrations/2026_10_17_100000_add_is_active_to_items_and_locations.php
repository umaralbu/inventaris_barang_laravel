<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('items', 'is_active')) {
            Schema::table('items', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        if (!Schema::hasColumn('locations', 'is_active')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('items', 'is_active')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('locations', 'is_active')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};