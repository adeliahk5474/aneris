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
        $verification = \App\Models\PortfolioVerification::where('artist_id', $artist->user_id)
            ->latest()
            ->first();
        $artistId = $artist->user_id;

        // ===============================
        // ACTIVE (sedang kerja)
        // ===============================
        $activeCommissions = Order::where('artist_id', $artistId)
            ->whereIn('status', ['in_progress', 'revision'])
            ->count();

        // ===============================
        // PENDING (belum di-accept artist)
        // ===============================
        $pendingCommissions = Order::where('artist_id', $artistId)
            ->where('status', 'pending')
            ->count();

        // ===============================
        // CLIENT UNIK
        // ===============================
        $activeClients = Order::where('artist_id', $artistId)
            ->distinct('client_id')
            ->count('client_id');

        // ===============================
        // EARNINGS (hanya completed)
        // ===============================
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
        // NOTIF (pending + waiting client)
        // ===============================
        $recentNotifications = Order::where('artist_id', $artistId)
            ->whereIn('status', ['pending', 'waiting_client'])
            ->count();

        // ===============================
        // CHART 12 BULAN (TETAP AMAN)
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

        $activeForOverview = $incomingOrders->filter(fn($o) => in_array($o->status, [
            'pending',
            'paid',
            'in_progress',
            'revision',
            'revision_requested',
            'waiting_client',
        ]));

        $myServices = CommissionService::where('artist_id', $artistId)
            ->with('category')
            ->latest()
            ->get();

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
            'incomingOrders',
            'activeForOverview',
            'verification',
            'myServices'
        ));
    }
}
