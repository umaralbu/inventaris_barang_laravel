<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@inventaris.com'],
            [
                'name' => 'Admin Inventaris',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::firstOrCreate(
            ['email' => 'pegawai@inventaris.com'],
            [
                'name' => 'Pegawai',
                'username' => 'pegawai',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );

        $this->call([
            StaffSeeder::class,
            LibraryInventorySeeder::class,
        ]);
    }
}
