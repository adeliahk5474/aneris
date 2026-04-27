<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Commission;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $commission = Commission::first();

        Order::create([
            'commission_id' => $commission->id,
            'status' => 'completed',
            'total' => 100.00,
            'payment_status' => 'paid',
        ]);
    }
}

