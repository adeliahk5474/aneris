<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Client;

class CartSeeder extends Seeder
{
    public function run()
    {
        $client = Client::first();

        Cart::create([
            'client_id' => $client->id,
            'status' => 'active',
        ]);
    }
}

