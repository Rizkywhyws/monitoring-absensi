<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@plniconplus.co.id'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}