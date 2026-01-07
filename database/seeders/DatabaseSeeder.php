<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@coms.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Cashier / Staff user
        User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@coms.com',
            'password' => Hash::make('cashier123'),
            'role' => 'staff',
        ]);
    }
}
