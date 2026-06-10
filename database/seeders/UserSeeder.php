<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'mobile' => '+963930028709',
            'role' => 'admin',
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Regular User',
            'email' => 'user@gmail.com',
            'mobile' => '+963930028708',
            'role' => 'user',
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);


    }
}
