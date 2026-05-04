<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CommissionService;
use App\Models\Order;
use App\Models\Review;

class ArtistDashboardController extends Controller
{
    public function index()
    {
        $artist = Auth::user();
        $artistId = $artist->user_id;

        // ===============================
        // STATISTIK (ORDER BASED)
        // ===============================

        // Sedang dikerjakan
        $activeCommissions = Order::where('artist_id', $artistId)
            ->where('status', 'in_progress')
            ->count();

        // Pending (belum diterima)
        $pendingCommissions = Order::where('artist_id', $artistId)
            ->where('status', 'pending')
            ->count();

        // Total client unik
        $activeClients = Order::where('artist_id', $artistId)
            ->distinct('client_id')
            ->count('client_id');

        // Earnings (hanya completed)
        $totalEarnings = Order::where('artist_id', $artistId)
            ->where('status', 'completed')
            ->sum('total_price');

        // ===============================
        // RATING
        // ===============================
        $averageRating = Review::whereHas('order', function ($q) use ($artistId) {
            $q->where('artist_id', $artistId);
        })->avg('rating');

        // ===============================
        // SERVICES
        // ===============================
        $totalServices = CommissionService::where('artist_id', $artistId)->count();

        // ===============================
        // NOTIF (order pending)
        // ===============================
        $recentNotifications = Order::where('artist_id', $artistId)
            ->where('status', 'pending')
            ->count();

        // ===============================
        // GRAFIK (12 BULAN)
        // ===============================
        $monthlyLabels = [];
        $monthlyEarnings = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            $monthlyLabels[] = $month->format('M');

            $monthlyEarnings[] = Order::where('artist_id', $artistId)
                ->where('status', 'completed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_price');
        }

        // ===============================
        // ORDER MASUK
        // ===============================
        $incomingOrders = Order::with(['client', 'service'])
            ->where('artist_id', $artistId)
            ->latest()
            ->take(10)
            ->get();

        // ===============================
        // RETURN VIEW
        // ===============================
        return view('dashboards.artist', compact(
            'artist',
            'activeCommissions',
            'pendingCommissions',
            'activeClients',
            'totalEarnings',
            'averageRating',
            'totalServices',
            'recentNotifications',
            'monthlyLabels',
            'monthlyEarnings',
            'incomingOrders'
        ));
    }
}
