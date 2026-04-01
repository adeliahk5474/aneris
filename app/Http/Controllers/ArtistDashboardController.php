<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CommissionService;
use App\Models\CommissionRequest;
use App\Models\Order;
use App\Models\Review;

class ArtistDashboardController extends Controller
{
    public function index()
    {
        $artist = Auth::user(); // Artist yang login

        // ===============================
        // Statistik Utama
        // ===============================

        // Komisi aktif
        $activeCommissions = CommissionRequest::where('artist_id', $artist->id)
                                ->where('status', 'active')
                                ->count();

        // Total pendapatan (semua commission requests selesai)
        $totalEarnings = CommissionRequest::where('artist_id', $artist->id)
                                ->where('status', 'completed')
                                ->sum('proposed_price');

        // Rating rata-rata dari Review yang terkait Order milik artist
        $averageRating = Review::whereHas('order', function($q) use ($artist) {
            $q->where('artist_id', $artist->id);
        })->avg('rating');

        // Jumlah karya/services yang ditawarkan
        $totalServices = CommissionService::where('artist_id', $artist->id)->count();

        // ===============================
        // Insight Tambahan
        // ===============================

        // Klien aktif (unik)
        $activeClients = CommissionRequest::where('artist_id', $artist->id)
                            ->whereIn('status', ['active','in_progress'])
                            ->distinct('client_id')
                            ->count('client_id');

        // Komisi menunggu persetujuan
        $pendingCommissions = CommissionRequest::where('artist_id', $artist->id)
                                ->where('status', 'pending')
                                ->count();

        // Notifikasi terbaru (ambil 5 terakhir)
        $recentNotifications = $artist->notifications()->latest()->take(5)->count();

        // ===============================
        // Grafik Pendapatan per Bulan (12 bulan terakhir)
        // ===============================
        $monthlyLabels = [];
        $monthlyEarnings = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->format('M');
            $monthlyEarnings[] = CommissionRequest::where('artist_id', $artist->id)
                                    ->where('status', 'completed')
                                    ->whereMonth('created_at', $month->month)
                                    ->whereYear('created_at', $month->year)
                                    ->sum('proposed_price');
        }

        // ===============================
        // Return view dengan semua data
        // ===============================
        return view('dashboards.artist', compact(
            'artist',
            'activeCommissions',
            'totalEarnings',
            'averageRating',
            'totalServices',
            'activeClients',
            'pendingCommissions',
            'recentNotifications',
            'monthlyLabels',
            'monthlyEarnings'
        ));
    }
}
