<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);

        User::factory()->create([
            'name' => 'Creative Studio',
            'email' => 'artist@example.com',
            'password' => Hash::make('password'),
            'role' => 'artist'
        ]);
    }
}