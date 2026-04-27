<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commission;
use App\Models\Artist;
use App\Models\Client;
use App\Models\Category;

class CommissionSeeder extends Seeder
{
    public function run(): void
    {
        $artist = Artist::first();
        $client = Client::first();
        $category = Category::first();

        if (!$artist || !$client || !$category) {
            $this->command->warn('⚠️  CommissionSeeder skipped: missing artist, client, or category.');
            return;
        }

        Commission::create([
            'artist_id' => $artist->id,
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Character Commission',
            'description' => 'Custom anime-style character illustration.',
            'price' => 100.00,
            'status' => 'pending',
        ]);
    }
}
