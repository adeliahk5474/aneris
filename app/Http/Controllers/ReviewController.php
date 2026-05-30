<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;
use App\Models\Review;
use App\Services\NotificationService;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'             => 'required|exists:orders,order_id',
            'overall_rating'       => 'required|integer|min:1|max:5',
            'comment'              => 'nullable|string|max:1500',
            'rating_quality'       => 'nullable|integer|min:1|max:5',
            'rating_timeliness'    => 'nullable|integer|min:1|max:5',
            'rating_communication' => 'nullable|integer|min:1|max:5',
            'rating_brief'         => 'nullable|integer|min:1|max:5',
            'rating_attitude'      => 'nullable|integer|min:1|max:5',
            'rating_revision'      => 'nullable|integer|min:1|max:5',
        ]);

        $order  = Order::findOrFail($request->order_id);
        $userId = Auth::user()->user_id;

        if ($order->status !== 'completed') {
            return back()->with('error', 'Review hanya bisa diberikan setelah order selesai.');
        }

        if ($order->client_id !== $userId && $order->artist_id !== $userId) {
            abort(403);
        }

        $reviewerType = $order->client_id === $userId ? 'client' : 'artist';

        $existing = Review::where('order_id', $order->order_id)
            ->where('reviewer_id', $userId)
            ->first();

        if ($existing) {
            return back()->with('error', 'Kamu sudah memberikan review untuk order ini.');
        }

        $review = Review::create([
            'order_id'             => $order->order_id,
            'reviewer_id'          => $userId,
            'reviewer_type'        => $reviewerType,
            'rating'               => $request->overall_rating,
            'comment'              => $request->comment,
            'rating_quality'       => $request->rating_quality,
            'rating_timeliness'    => $request->rating_timeliness,
            'rating_communication' => $request->rating_communication,
            'rating_brief'         => $request->rating_brief,
            'rating_attitude'      => $request->rating_attitude,
            'rating_revision'      => $request->rating_revision,
            'is_visible'           => false,
        ]);

        // Reveal jika sudah bisa (keduanya submit / 14 hari)
        $review->maybeReveal();

        // Notif ke pihak lain
        $notifyTarget = $reviewerType === 'client' ? $order->artist_id : $order->client_id;
        NotificationService::reviewSubmitted(
            $notifyTarget,
            $order->order_id,
            Auth::user()->name
        );

        return back()->with('success', 'Review berhasil dikirim! Akan tampil setelah kedua pihak submit review.');
    }

    public function revealExpired(): int
    {
        $count = 0;
        Review::where('is_visible', false)
            ->where('created_at', '<=', now()->subDays(14))
            ->chunk(100, function ($reviews) use (&$count) {
                foreach ($reviews as $review) {
                    $review->update(['is_visible' => true, 'revealed_at' => now()]);
                    $count++;
                }
            });
        return $count;
    }
}
