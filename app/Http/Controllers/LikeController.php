<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Like;
use App\Models\CommissionService;
use App\Models\Artwork;
use App\Models\Notification;
use App\Services\NotificationService;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ═══════════════════════════════════════════
     |  TOGGLE LIKE
     |  POST /like
     |  body: { likeable_id, likeable_type }
    ═══════════════════════════════════════════ */
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_id'   => 'required|string',
            'likeable_type' => 'required|in:artwork,commission_service',
        ]);

        $userId       = Auth::user()->user_id;
        $likeableId   = $request->likeable_id;
        $likeableType = $request->likeable_type;

        $existing = Like::where('user_id', $userId)
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->first();

        if ($existing) {
            // Unlike
            $existing->delete();
            $likeCount = $this->getLikeCount($likeableId, $likeableType);
            $this->updateLikeCount($likeableId, $likeableType, $likeCount);

            if ($request->expectsJson()) {
                return response()->json(['liked' => false, 'like_count' => $likeCount]);
            }
            return back();
        }

        // Like
        Like::create([
            'like_id'       => (string) Str::uuid(),
            'user_id'       => $userId,
            'likeable_id'   => $likeableId,
            'likeable_type' => $likeableType,
            'created_at'    => now(),
        ]);

        $likeCount = $this->getLikeCount($likeableId, $likeableType);
        $this->updateLikeCount($likeableId, $likeableType, $likeCount);

        // Kirim notifikasi ke pemilik
        $this->notifyOwner($userId, $likeableId, $likeableType, $likeCount);

        if ($request->expectsJson()) {
            return response()->json(['liked' => true, 'like_count' => $likeCount]);
        }
        return back();
    }

    /* ─────────────────────────────────────────────
     | PRIVATE HELPERS
    ───────────────────────────────────────────── */

    private function getLikeCount(string $likeableId, string $likeableType): int
    {
        return Like::where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->count();
    }

    private function updateLikeCount(string $likeableId, string $likeableType, int $count): void
    {
        try {
            if ($likeableType === 'commission_service') {
                CommissionService::where('service_id', $likeableId)
                    ->update(['like_count' => $count]);
            }
            // Artwork: aktifkan jika tabel artworks sudah punya kolom like_count
            // elseif ($likeableType === 'artwork') {
            //     Artwork::where('artwork_id', $likeableId)->update(['like_count' => $count]);
            // }
        } catch (\Exception $e) {
            // skip jika kolom belum ada
        }
    }

    private function notifyOwner(
        string $likerId,
        string $likeableId,
        string $likeableType,
        int    $likeCount
    ): void {
        try {
            $liker    = Auth::user();
            $ownerId  = null;
            $itemName = '';
            $metadata = ['likeable_id' => $likeableId, 'likeable_type' => $likeableType];

            if ($likeableType === 'commission_service') {
                $service = CommissionService::where('service_id', $likeableId)->first();
                if (!$service) return;
                $ownerId  = $service->artist_id;
                $itemName = $service->title ?? 'jasa commission kamu';
                $metadata['service_id'] = $likeableId;
            } elseif ($likeableType === 'artwork') {
                // Aktifkan saat model Artwork sudah ada
                // $artwork = Artwork::where('artwork_id', $likeableId)->first();
                // if (!$artwork) return;
                // $ownerId  = $artwork->artist_id ?? $artwork->user_id;
                // $itemName = $artwork->title ?? $artwork->caption ?? 'artwork kamu';
                return;
            }

            // Jangan notif diri sendiri
            if (!$ownerId || $ownerId === $likerId) return;

            $typeLabel = $likeableType === 'artwork' ? 'artwork' : 'jasa commission';

            if ($likeCount === 1) {
                // Like pertama — notif langsung
                $message = "❤️ {$liker->name} menyukai {$typeLabel} \"{$itemName}\" milikmu.";

                NotificationService::send($ownerId, $message, 'like', $metadata);
            } else {
                // Like ke-2+ — sistem digest:
                // cari notif like unread untuk item ini, update pesannya
                // supaya tidak banjir notif
                $existingNotif = Notification::where('user_id', $ownerId)
                    ->where('type', 'like')
                    ->where('status', 'unread')
                    ->whereRaw("metadata::text LIKE ?", ["%\"likeable_id\":\"{$likeableId}\"%"])
                    ->latest()
                    ->first();

                $others  = $likeCount - 1;
                $message = "❤️ {$liker->name} dan {$others} orang lainnya menyukai {$typeLabel} \"{$itemName}\" milikmu.";

                if ($existingNotif) {
                    // Update pesan notif yang sudah ada (digest)
                    $existingNotif->update(['message' => $message]);
                } else {
                    // Tidak ada notif unread sebelumnya, buat baru
                    NotificationService::send($ownerId, $message, 'like', $metadata);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Like notify failed: ' . $e->getMessage());
        }
    }
}
