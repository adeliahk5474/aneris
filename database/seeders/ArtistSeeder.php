<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artist;
use App\Models\User;

class ArtistSeeder extends Seeder
{
    public function run()
    {
        $artistUser = User::where('role', 'artist')->first();

        Artist::create([
            'user_id' => $artistUser->id,
            'specialization' => '2D Illustration',
            'status' => 'active',
            'rating' => 4.9,
            'total_orders' => 10,
        ]);
    }
}

