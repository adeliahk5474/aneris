<?php
// app/Http/Controllers/ExploreController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\CommissionService;
use App\Models\Like;
use App\Models\User;
use App\Models\Category;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->search;
        $category = $request->category;
        $sort     = $request->get('sort', 'popular'); // popular | newest | top_rated | random

        // ── CATEGORIES
        $categories = Category::orderBy('name')->get();

        // ── USER SEARCH (hanya saat ada keyword)
        $users = collect();
        if ($search) {
            $users = User::where('name', 'ILIKE', "%{$search}%")
                ->latest()
                ->take(20)
                ->get();
        }

        // ── ARTWORKS
        $artworksQuery = Artwork::with(['user', 'category'])
            ->where('status', 'published')
            ->when($search, fn($q) =>
                $q->where('caption', 'ILIKE', "%{$search}%")
            )
            ->when($category, fn($q) =>
                $q->whereHas('category', fn($q2) =>
                    $q2->where('name', 'ILIKE', "%{$category}%")
                )
            );

        // ── COMMISSION SERVICES
        $servicesQuery = CommissionService::with(['artist', 'category'])
            ->where('status', 'active')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('title', 'ILIKE', "%{$search}%")
                       ->orWhere('description', 'ILIKE', "%{$search}%");
                });
            })
            ->when($category, fn($q) =>
                $q->whereHas('category', fn($q2) =>
                    $q2->where('name', 'ILIKE', "%{$category}%")
                )
            );

        // ── APPLY SORT ke services
        switch ($sort) {
            case 'newest':
                $servicesQuery->latest();
                $artworksQuery->latest();
                break;

            case 'top_rated':
                $servicesQuery->orderByDesc('avg_rating')
                              ->orderByDesc('review_count');
                $artworksQuery->latest();
                break;

            case 'popular':
            default:
                // Weighted score: rating + like + order
                $servicesQuery->orderByRaw("
                    (
                        (avg_rating * LOG(review_count + 2))
                        + (order_count * 0.1)
                        + (like_count * 0.05)
                    ) DESC
                ");
                $artworksQuery->latest();
                break;
        }

        $artworks = $artworksQuery->take(30)->get();
        $services = $servicesQuery->take(30)->get();

        // ── LIKE STATUS untuk user yang login
        $likedServiceIds = collect();
        if (auth()->check()) {
            $allServiceIds = $services->pluck('service_id');
            $likedServiceIds = Like::where('user_id', auth()->user()->user_id)
                ->where('likeable_type', 'commission_service')
                ->whereIn('likeable_id', $allServiceIds)
                ->pluck('likeable_id')
                ->flip(); // jadi collection key untuk O(1) lookup
        }

        // ── MERGE & BUILD EXPLORE FEED
        $artworkItems = $artworks->map(function ($item) {
            $item->type = 'artwork';
            return $item;
        });

        $serviceItems = $services->map(function ($item) use ($likedServiceIds) {
            $item->type = 'service';
            $item->is_liked = $likedServiceIds->has($item->service_id);
            $catName = $item->category->name ?? 'Commission';
            $item->catName = $catName;
            $item->isLive2D = strtolower($catName) === 'live2d';
            $item->lc = $item->like_count ?? 0;
            $item->avgR = $item->avg_rating ?? 0;
            return $item;
        });

        $explore = match ($sort) {
            'random'  => $artworkItems->merge($serviceItems)->shuffle()->values(),
            'newest'  => $artworkItems->merge($serviceItems)
                            ->sortByDesc('created_at')->values(),
            // popular & top_rated: services lebih dulu (sudah di-sort), artwork di antara
            default   => $serviceItems->merge($artworkItems)->values(),
        };

        return view('page.explore', compact(
            'explore',
            'users',
            'categories',
            'search',
            'category',
            'sort'
        ));
    }
}