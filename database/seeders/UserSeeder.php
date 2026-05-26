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
            'email' => 'ahm@gmail.com',
            'mobile' => '+963982786609',
            'role' => 'user',
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Regular User',
            'email' => 'moh@gmail.com',
            'mobile' => '+963982786604',
            'role' => 'user',
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);


        User::query()->create([
            'name' => 'Regular User',
            'email' => 'san@gmail.com',
            'mobile' => '+963982786339',
            'role' => 'user',
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Regular User',
            'email' => 'hass@gmail.com',
            'mobile' => '+963982786989',
            'role' => 'user',
            'status' => 'active',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
