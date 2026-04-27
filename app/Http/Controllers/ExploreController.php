<?php

namespace App\Http\Controllers;

use App\Models\Artwork; // pastikan model ada
use App\Models\CommissionService;

class ExploreController extends Controller
{
    public function index()
    {
        $artworks = Artwork::latest()->take(30)->get(); // sementara ambil 30 artwork acak
        $services = CommissionService::where('status', 'active')
            ->latest()
            ->take(20)
            ->get();
        // gabungkan + kasih type
        $explore = $artworks->map(function ($item) {
            $item->type = 'artwork';
            return $item;
        })->merge(
            $services->map(function ($item) {
                $item->type = 'service';
                return $item;
            })
        )->shuffle(); // biar campur random
        return view('page.explore', compact('explore'));
    }
}
