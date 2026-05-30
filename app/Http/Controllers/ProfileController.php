<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        $isOwner = auth()->check() && auth()->user()->user_id === $user->user_id;
        $isArtist = $user->role === 'artist';
        $isFollowing = auth()->check()
            ? $user->followers->contains('user_id', auth()->user()->user_id)
            : false;

        $followerCount = $user->followers->count();
        $followingCount = $user->following->count();
        $postCount = $user->artworks->count();

        $clientBadges = [];
        if (! $isArtist) {
            $totalOrders = $user->ordersAsClient()->count();

            if ($totalOrders >= 5) {
                $clientBadges[] = ['icon' => 'bi-bag-check-fill', 'label' => 'Trusted Buyer', 'color' => '#22c55e'];
            }
            if ($totalOrders >= 1) {
                $clientBadges[] = ['icon' => 'bi-patch-check-fill', 'label' => 'Good Client', 'color' => '#8b5cf6'];
            }

            $clientBadges[] = ['icon' => 'bi-lightning-charge-fill', 'label' => 'Fast Response', 'color' => '#facc15'];

            $excessRevision = $user->ordersAsClient()->where('revision_count', '>', 2)->count();
            if ($excessRevision === 0 && $totalOrders > 0) {
                $clientBadges[] = ['icon' => 'bi-file-text-fill', 'label' => 'Clear Brief', 'color' => '#38bdf8'];
            }
        }

        $reviewStats = null;
        if ($isArtist) {
            $artistReviews = Review::whereHas('order', fn($q) => $q->where('artist_id', $user->user_id))
                ->where('reviewer_type', 'client')
                ->where('is_visible', true)
                ->get();

            $reviewStats = [
                'count' => $artistReviews->count(),
                'avg_overall' => $artistReviews->avg('rating'),
                'avg_quality' => $artistReviews->avg('rating_quality'),
                'avg_timeliness' => $artistReviews->avg('rating_timeliness'),
                'avg_communication' => $artistReviews->avg('rating_communication'),
            ];
        }

        $clientOrders = collect();
        $completedCount = 0;
        $totalSpent = 0;
        $artistsHired = 0;

        if (! $isArtist && $isOwner) {
            $clientOrders = $user->ordersAsClient()->with(['artist', 'service'])->latest()->get();
            $completedCount = $clientOrders->where('status', 'completed')->count();
            $totalSpent = $clientOrders->where('status', 'completed')->sum('total_price');
            $artistsHired = $clientOrders->pluck('artist_id')->unique()->count();
        }

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
            // Hapus avatar lama dari Cloudinary
            \App\Services\CloudinaryService::delete($user->avatar);

            // Upload baru
            $user->avatar = \App\Services\CloudinaryService::upload(
                $request->file('profile_picture'),
                'avatars'
            );
        }

        $user->save();

        return redirect()->route('profile.show', $user->user_id)
            ->with('success', 'Profile updated successfully.');
    }
}
