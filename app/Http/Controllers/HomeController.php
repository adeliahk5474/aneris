<?php

namespace App\Http\Controllers;

use App\Models\CommissionService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil layanan aktif beserta artisnya
        $services = CommissionService::with('artist')
            ->where('status', 'active')
            ->latest()
            ->take(9)
            ->get();

        // Cek apakah user sedang login
        $user = Auth::user();

        return view('homepage.home', compact('services', 'user'));
    }
}
