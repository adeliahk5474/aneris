<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artwork;
use App\Models\Artist;

class ArtworkSeeder extends Seeder
{
    public function run()
    {
        $artist = Artist::first();

        Artwork::create([
            'artist_id' => 1,
            'category_id' => 1,
            'title' => 'Fantasy Portrait',
            'description' => 'A vibrant fantasy-themed digital artwork.',
            'image_path' => 'artworks/fantasy_portrait.png',
            'price' => 120.00,
        ]);
    }
}

