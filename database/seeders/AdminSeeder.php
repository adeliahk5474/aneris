<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'adeliahk54742@gmail.com'],
            [
                'name'     => 'Aneris Admin',
                'email'    => 'adeliahk5474@gmail.com',
                'password' => bcrypt('Adelia5474'),
            ]
        );
    }
}
