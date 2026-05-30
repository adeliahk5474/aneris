<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\CommissionService;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // ── TOP RATED: Bayesian ranking + like_count bonus
        // Formula: (avg_rating × log(review_count+2)) + (order_count × 0.1) + (like_count × 0.05)
        $services = CommissionService::with(['artist', 'category'])
            ->active()
            ->orderByRaw("
                (
                    (avg_rating * LOG(review_count + 2))
                    + (order_count * 0.1)
                    + (like_count * 0.05)
                ) DESC
            ")
            ->take(12)
            ->get();

        // ── MOST LIKED: berdasarkan like_count tertinggi
        $mostLiked = CommissionService::with(['artist', 'category'])
            ->active()
            ->where('like_count', '>', 0)
            ->orderByDesc('like_count')
            ->take(8)
            ->get();

        // ── RISING ARTISTS: service baru, belum banyak review
        $newServices = CommissionService::with(['artist', 'category'])
            ->active()
            ->where('review_count', '<', 3)
            ->latest()
            ->take(8)
            ->get();

        // ── CATEGORIES
        $categories = Category::orderBy('name')->get();

        // ── TOTAL
        $totalServices = CommissionService::active()->count();

        // ── FEED ARTWORK
        $feed = Artwork::with('user')
            ->where('status', 'published')
            ->latest()
            ->take(24)
            ->get();

        return view('homepage.home', compact(
            'services',
            'mostLiked',
            'newServices',
            'categories',
            'totalServices',
            'feed'
        ));
    }
}