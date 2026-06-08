<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CommissionService;

class ProfileController extends Controller
{
    // ===========================
    // TAMPILKAN PROFILE
    // ===========================
    public function show($id)
    {
        $user = User::with(['followers', 'following', 'artworks'])
            ->where('user_id', $id)
            ->firstOrFail();

        $isOwner    = auth()->check() && auth()->user()->user_id === $user->user_id;
        $isArtist   = $user->role === 'artist';
        $isFollowing = auth()->check()
            ? $user->followers->contains('user_id', auth()->user()->user_id)
            : false;

        $followerCount  = $user->followers->count();
        $followingCount = $user->following->count();
        $postCount      = $user->artworks->count();

        // ── CLIENT BADGES ────────────────────────────────────
        $clientBadges = [];

        if (!$isArtist) {
            $totalOrders = $user->ordersAsClient()->count();

            // Ambil review yang diterima client (dari artist → client)
            $reviewsReceived = Review::whereHas('order', fn($q) => $q->where('client_id', $user->user_id))
                ->where('reviewer_type', 'artist')
                ->where('is_visible', true)
                ->get();

            $reviewCount  = $reviewsReceived->count();
            $avgRating    = $reviewCount > 0 ? $reviewsReceived->avg('rating') : null;
            $avgBrief     = $reviewCount > 0 ? $reviewsReceived->avg('rating_brief') : null;
            $avgAttitude  = $reviewCount > 0 ? $reviewsReceived->avg('rating_attitude') : null;
            $avgRevision  = $reviewCount > 0 ? $reviewsReceived->avg('rating_revision') : null;

            // ── Good Client: rata-rata overall ≥ 4.0 dari minimal 1 review
            if ($reviewCount >= 5 && $avgRating >= 4.0) {
                $clientBadges[] = [
                    'icon'  => 'bi-patch-check-fill',
                    'label' => 'Good Client',
                    'color' => '#8b5cf6',
                ];
            }

            // ── Trusted Buyer: sudah 5+ order DAN rating rata-rata ≥ 4.0
            if ($totalOrders >= 5 && $reviewCount >= 5 && $avgRating >= 4.0) {
                $clientBadges[] = [
                    'icon'  => 'bi-bag-check-fill',
                    'label' => 'Trusted Buyer',
                    'color' => '#22c55e',
                ];
            }

            // ── Fast Response: badge attitude ≥ 4.5 (artist nilai respon client tinggi)
            //    atau tidak ada review negatif soal komunikasi
            if ($reviewCount >= 5 && $avgAttitude !== null && $avgAttitude >= 4.0) {
                $clientBadges[] = [
                    'icon'  => 'bi-lightning-charge-fill',
                    'label' => 'Fast Response',
                    'color' => '#facc15',
                ];
            }

            // ── Clear Brief: rating_brief rata-rata ≥ 4.0
            if ($reviewCount >= 5 && $avgBrief !== null && $avgBrief >= 4.0) {
                // Pastikan tidak ada excess revision yang banyak
                $excessRevision = $user->ordersAsClient()->where('revision_count', '>', 3)->count();
                if ($excessRevision === 0 || $totalOrders === 0) {
                    $clientBadges[] = [
                        'icon'  => 'bi-file-text-fill',
                        'label' => 'Clear Brief',
                        'color' => '#38bdf8',
                    ];
                }
            }
        }

        // ── ARTIST REVIEW STATS ──────────────────────────────
        $reviewStats = null;
        if ($isArtist) {
            $artistReviews = Review::whereHas('order', fn($q) => $q->where('artist_id', $user->user_id))
                ->where('reviewer_type', 'client')
                ->where('is_visible', true)
                ->get();

            $reviewStats = [
                'count'             => $artistReviews->count(),
                'avg_overall'       => $artistReviews->avg('rating'),
                'avg_quality'       => $artistReviews->avg('rating_quality'),
                'avg_timeliness'    => $artistReviews->avg('rating_timeliness'),
                'avg_communication' => $artistReviews->avg('rating_communication'),
            ];
        }

        // ── CLIENT ORDER DATA (hanya untuk owner) ───────────
        $clientOrders   = collect();
        $completedCount = 0;
        $totalSpent     = 0;
        $artistsHired   = 0;

        if (!$isArtist && $isOwner) {
            $clientOrders   = $user->ordersAsClient()->with(['artist', 'service'])->latest()->get();
            $completedCount = $clientOrders->where('status', 'completed')->count();
            $totalSpent     = $clientOrders->where('status', 'completed')->sum('total_price');
            $artistsHired   = $clientOrders->pluck('artist_id')->unique()->count();
        }

        // ── REVIEWS TAB ──────────────────────────────────────
        if ($isArtist) {
            $reviews = Review::whereHas('order', fn($q) => $q->where('artist_id', $user->user_id))
                ->where('reviewer_type', 'client')
                ->where('is_visible', true)
                ->with('reviewer', 'order.service')
                ->latest()
                ->get();
        } else {
            $reviews = $user->reviews()
                ->where('reviewer_type', 'client')
                ->where('is_visible', true)
                ->with(['reviewer', 'order.service'])
                ->latest()
                ->get();
        }

        $artworks = $user->artworks()->latest()->get();

        $commissionServices = $user->commissionServices()
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('dashboards.profile', compact(
            'user',
            'isOwner',
            'isArtist',
            'isFollowing',
            'followerCount',
            'followingCount',
            'postCount',
            'clientBadges',
            'reviewStats',
            'clientOrders',
            'completedCount',
            'totalSpent',
            'artistsHired',
            'reviews',
            'artworks',
            'commissionServices'
        ));
    }

    // ===========================
    // UPDATE PROFILE
    // ===========================
    public function update(Request $request, $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        if (Auth::user()->user_id !== $user->user_id) abort(403);

        $request->validate([
            'name'            => 'required|string|max:100',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $user->name = $request->name;

        if ($request->hasFile('profile_picture')) {
            \App\Services\StorageService::delete($user->avatar);
            $user->avatar = \App\Services\StorageService::upload(
                $request->file('profile_picture'),
                'avatars'
            );
        }

        $user->save();

        return redirect()->route('profile.show', $user->user_id)
            ->with('success', 'Profile updated successfully.');
    }
}
