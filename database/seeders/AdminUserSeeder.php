<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // <-- Import the User model
use Illuminate\Support\Facades\Hash; // <-- Import Hash facade
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@coms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // change after first login
                'role' => 'admin'
            ]
        );
    }
}
