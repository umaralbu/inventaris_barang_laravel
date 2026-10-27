<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->unique()->after('name');
            });
        }

        $users = DB::table('users')->whereNull('username')->orderBy('id')->get(['id', 'name', 'email']);

        foreach ($users as $user) {
            $baseUsername = Str::slug(Str::before((string) $user->email, '@'), '_');
            $baseUsername = $baseUsername !== '' ? $baseUsername : 'user' . $user->id;
            $username = $baseUsername;
            $suffix = 1;

            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $baseUsername . $suffix;
                $suffix++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            });
        }
    }
};