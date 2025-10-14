<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $clientUser = User::where('role', 'client')->first();

        Client::create([
            'user_id' => $clientUser->id,
            'total_spent' => 250.00,
            'total_orders' => 3,
        ]);
    }
}

