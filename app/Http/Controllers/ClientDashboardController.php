<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;

class ClientDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard untuk client.
     */
    public function index()
    {
        $user = Auth::user();

        // Pastikan user memiliki data client
        $client = Client::where('user_id', $user->id)->first();

        // Jika belum punya data client, bisa diarahkan atau buat otomatis
        if (!$client) {
            $client = Client::create([
                'user_id' => $user->id,
                'total_orders' => 0,
                'total_spent' => 0,
            ]);
        }

        return view('dashboard.client', compact('user', 'client'));
    }
}
