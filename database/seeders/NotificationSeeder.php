<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Welcome to Aneris Art!',
            'message' => 'Thank you for joining our creative platform.',
            'is_read' => false,
        ]);
    }
}
