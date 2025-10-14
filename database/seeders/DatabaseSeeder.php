<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ArtistSeeder::class,
            ClientSeeder::class,
            ArtistProfileSeeder::class,
            CategorySeeder::class,
            ArtworkSeeder::class,
            CommissionSeeder::class,
            OrderSeeder::class,
            CartSeeder::class,
            NotificationSeeder::class,
        ]);
    }

}
