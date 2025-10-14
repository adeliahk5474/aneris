<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArtistProfile;
use App\Models\Artist;

class ArtistProfileSeeder extends Seeder
{
    public function run()
    {
        $artist = Artist::first();

        ArtistProfile::create([
            'artist_id' => $artist->id,
            'bio' => 'Professional digital artist specializing in anime illustrations.',
            'portfolio_link' => 'https://example.com/portfolio',
            'profile_image' => 'artist_profile.png',
        ]);
    }
}

