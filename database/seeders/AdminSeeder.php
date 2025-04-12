<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@flonewell.com'],
            [
                'name' => 'Admin User',
                'phone' => '254712345678',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'water_units' => 0,
                'balance' => 0,
            ]
        );
    }
}