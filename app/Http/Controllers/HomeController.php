<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\CommissionService;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index()
    {
        // Ambil commission terbaru atau artwork terbaru
        // limit memory usage by paginating the feed (loads only a page at a time)
        $feed = Artwork::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(24);
        $services = CommissionService::with('artist')
            ->where('status', 'active')
            ->latest()
            ->take(12)
            ->get();
        // gabungkan & urutkan
        $combined = $feed->map(function ($item) {
            $item->type = 'artwork';
            return $item;
        })->merge(
            $services->map(function ($item) {
                $item->type = 'service';
                return $item;
            })
        )->sortByDesc('created_at');


        return view('homepage.home', ['feed' => $combined]);
    }
}
