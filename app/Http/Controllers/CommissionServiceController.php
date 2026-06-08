<?php
// app/Http/Controllers/CommissionServiceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommissionService;
use App\Models\Order;
use App\Models\Review;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class CommissionServiceController extends Controller
{
    /* ═══════════════════════════════════════════
     |  SHOW — halaman detail jasa commission
    ═══════════════════════════════════════════ */
    public function show($id)
    {
        $service = CommissionService::with(['artist', 'category'])
            ->where('service_id', $id)
            ->firstOrFail();

        // Queue aktif
        $activeOrders = Order::with(['client'])
            ->where('service_id', $id)
            ->whereIn('status', ['paid', 'in_progress', 'revision', 'waiting_client'])
            ->latest()
            ->take(5)
            ->get();

        $activeCount   = $activeOrders->count();
        $waitlistCount = Order::where('service_id', $id)->where('status', 'pending')->count();

        // Reviews visible saja (blind review)
        $reviews = Review::with(['reviewer'])
            ->whereHas('order', fn($q) => $q->where('service_id', $id))
            ->where('reviewer_type', 'client')
            ->where('is_visible', true)
            ->latest()
            ->take(8)
            ->get();

        $avgRating   = $service->avg_rating ?: $reviews->avg('rating') ?: 0;
        $reviewCount = $reviews->count();

        $addons       = $service->addons ?? [];
        $defaultPrice = (float) ($service->base_price ?? 0);
        $galleryImages = $service->gallery_images ?? [];

        $willList = method_exists($service, 'willDoList')
            ? $service->willDoList()
            : ($service->will_do
                ? array_filter(array_map('trim', explode("\n", $service->will_do)))
                : []);

        $wontList = method_exists($service, 'wontDoList')
            ? $service->wontDoList()
            : ($service->wont_do
                ? array_filter(array_map('trim', explode("\n", $service->wont_do)))
                : []);

        $slotsUsed  = $activeCount;
        $slotsTotal = $service->queue_slots ?? 5;
        $slotsLeft  = max(0, $slotsTotal - $slotsUsed);
        $slotPct    = $slotsTotal > 0 ? min(100, round(($slotsUsed / $slotsTotal) * 100)) : 0;

        $isArtist = Auth::check() && Auth::user()->role === 'artist';
        $isOwner  = Auth::check() && Auth::user()->user_id === $service->artist_id;

        $hasPendingOrder = Auth::check()
            ? Order::where('client_id', Auth::user()->user_id)
            ->where('service_id', $service->service_id)
            ->where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->exists()
            : false;

        $isLiked = Auth::check()
            ? Like::where('user_id', Auth::user()->user_id)
            ->where('likeable_id', $service->service_id)
            ->where('likeable_type', 'commission_service')
            ->exists()
            : false;

        $likeCount = $service->like_count ?? 0;

        $allImages = array_values(array_filter(array_merge(
            [$service->image_url ?? asset('images/default-thumb.png')],
            is_array($galleryImages) ? $galleryImages : []
        )));

        return view('commission.detail', compact(
            'service',
            'activeOrders',
            'activeCount',
            'waitlistCount',
            'slotsLeft',
            'reviews',
            'avgRating',
            'addons',
            'defaultPrice',
            'willList',
            'wontList',
            'reviewCount',
            'slotsUsed',
            'slotsTotal',
            'slotPct',
            'isArtist',
            'isOwner',
            'hasPendingOrder',
            'isLiked',
            'likeCount',
            'allImages'
        ));
    }

    /* ═══════════════════════════════════════════
     |  UPDATE
    ═══════════════════════════════════════════ */
    public function update(Request $request, $id)
    {
        $service = CommissionService::where('service_id', $id)->firstOrFail();
        if ($service->artist_id !== Auth::user()->user_id) abort(403);

        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category_id'    => 'nullable|exists:categories,category_id',
            'base_price'     => 'required|numeric|min:1000',
            'estimated_days' => 'nullable|integer|min:1|max:365',
            'max_revisions'  => 'nullable|integer|min:0|max:20',
            'queue_slots'    => 'nullable|integer|min:1|max:50',
            'image'          => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
            'gallery.*'      => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
        ]);

        if ($request->hasFile('image')) {
            \App\Services\StorageService::delete($service->image_url);
            $service->image_url = \App\Services\StorageService::upload(
                $request->file('image'),
                'commissions'
            );
        }

        $gallery = $service->gallery_images ?? [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if (count($gallery) >= 3) break;
                $gallery[] = \App\Services\StorageService::upload(
                    $file,
                    'commissions/gallery'
                );
            }
        }

        $service->update([
            'title'          => $request->title,
            'description'    => $request->description,
            'will_do'        => $request->will_do,
            'wont_do'        => $request->wont_do,
            'category_id'    => $request->category_id,
            'base_price'     => $request->base_price,
            'estimated_days' => $request->estimated_days ?? $service->estimated_days,
            'max_revisions'  => $request->max_revisions ?? $service->max_revisions,
            'queue_slots'    => $request->queue_slots ?? $service->queue_slots,
            'turnaround'     => $request->turnaround ?? $service->turnaround,
            'addons'         => $request->addons ?? $service->addons,
            'gallery_images' => $gallery,
        ]);

        return back()->with('success', 'Commission updated.');
    }

    /* ═══════════════════════════════════════════
     |  DESTROY
    ═══════════════════════════════════════════ */
    public function destroy($id)
    {
        $service = CommissionService::where('service_id', $id)->firstOrFail();

        if ($service->artist_id !== Auth::user()->user_id) abort(403);

        $service->delete();

        return back()->with('success', 'Commission deleted.');
    }
}
