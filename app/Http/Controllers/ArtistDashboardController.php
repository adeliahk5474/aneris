<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\CommissionService;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ArtistDashboardController extends Controller
{
    /**
     * Tampilkan dashboard artist
     */
    public function index()
    {
        $artist = Auth::user();

        // Komisi aktif (pending, in_progress, revision)
        $activeCommissions = Order::where('artist_id', $artist->user_id)
                                  ->whereIn('status', ['pending','in_progress','revision'])
                                  ->count();

        // Total pendapatan (completed orders)
        $totalEarnings = Order::where('artist_id', $artist->user_id)
                              ->where('status', 'completed')
                              ->sum('total_price');

        // Rating rata-rata
        $averageRating = Review::whereHas('order', function($q) use ($artist) {
            $q->where('artist_id', $artist->user_id);
        })->avg('rating');

        // Portofolio artworks
        $artworks = Artwork::where('artist_id', $artist->user_id)
                           ->latest()
                           ->get();

        // Commission services
        $services = CommissionService::where('artist_id', $artist->user_id)
                                     ->latest()
                                     ->get();

        return view('dashboards.artist', compact(
            'artist',
            'activeCommissions',
            'totalEarnings',
            'averageRating',
            'artworks',
            'services'
        ));
    }

    /**
     * Tambah artwork baru (portofolio artist)
     */
    public function storeArtwork(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|uuid|exists:categories,category_id',
            'description' => 'nullable|string',
            'file_url' => 'nullable|string',
            'preview_url' => 'nullable|string',
            'status' => 'nullable|in:public,private,draft',
        ]);

        Artwork::create([
            'artist_id' => auth()->user()->user_id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $request->file_url,
            'preview_url' => $request->preview_url,
            'price' => 0, // portofolio artist tidak ada harga
            'status' => $request->status ?? 'public',
        ]);

        return back()->with('success', 'Artwork berhasil ditambahkan.');
    }

    /**
     * Hapus artwork
     */
    public function destroyArtwork($id)
    {
        $artwork = Artwork::where('artist_id', auth()->user()->user_id)
                          ->where('artwork_id', $id)
                          ->firstOrFail();

        $artwork->delete();

        return back()->with('success', 'Artwork berhasil dihapus.');
    }

    /**
     * Tambah commission service (jasa)
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|uuid|exists:categories,category_id',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        CommissionService::create([
            'artist_id' => auth()->user()->user_id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price ?? 0,
            'status' => $request->status ?? 'active',
        ]);

        return back()->with('success', 'Commission service berhasil ditambahkan.');
    }

    /**
     * Hapus commission service
     */
    public function destroyService($id)
    {
        $service = CommissionService::where('artist_id', auth()->user()->user_id)
                                    ->where('commission_service_id', $id)
                                    ->firstOrFail();

        $service->delete();

        return back()->with('success', 'Commission service berhasil dihapus.');
    }
}
