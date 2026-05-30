<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Core method — kirim notifikasi ke satu user.
     * Semua method lain memanggil ini.
     */
    public static function send(
        string $userId,
        string $message,
        string $type = 'system',
        array  $metadata = []
    ): void {
        try {
            Notification::create([
                'user_id'  => $userId,
                'message'  => $message,
                'type'     => $type,
                'status'   => 'unread',
                'metadata' => !empty($metadata) ? $metadata : null,
            ]);
        } catch (\Throwable $e) {
            // Notif gagal tidak boleh merusak flow utama
            Log::warning('[NotificationService] Gagal kirim notifikasi', [
                'user_id' => $userId,
                'message' => $message,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /* ══════════════════════════════════════════
     |  ORDER EVENTS
    ══════════════════════════════════════════ */

    /** Order berhasil dibayar → notif ke ARTIST */
    public static function orderPaid(
        string $artistId,
        string $orderId,
        string $clientName,
        string $serviceTitle
    ): void {
        self::send(
            $artistId,
            "💰 Order baru dari {$clientName} untuk \"{$serviceTitle}\" telah dibayar. Segera konfirmasi!",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Artist terima order → notif ke CLIENT */
    public static function orderAccepted(
        string $clientId,
        string $orderId,
        string $artistName,
        string $serviceTitle
    ): void {
        self::send(
            $clientId,
            "✅ {$artistName} telah menerima ordermu untuk \"{$serviceTitle}\". Pengerjaan dimulai!",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Artist tolak order → notif ke CLIENT */
    public static function orderRejected(
        string $clientId,
        string $orderId,
        string $artistName,
        string $serviceTitle
    ): void {
        self::send(
            $clientId,
            "❌ {$artistName} tidak bisa menerima ordermu untuk \"{$serviceTitle}\". Order dibatalkan.",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Artist kirim hasil ke client → notif ke CLIENT */
    public static function resultSent(
        string $clientId,
        string $orderId,
        string $artistName,
        string $phase
    ): void {
        $phaseLabel = match ($phase) {
            'sketch'   => 'Sketch',
            'coloring' => 'Coloring',
            'final'    => 'Final',
            default    => ucfirst($phase),
        };
        self::send(
            $clientId,
            "🎨 {$artistName} telah mengirim hasil {$phaseLabel}. Silakan review dan approve!",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Client approve phase → notif ke ARTIST */
    public static function phaseApproved(
        string $artistId,
        string $orderId,
        string $clientName,
        string $nextPhase
    ): void {
        $nextLabel = match ($nextPhase) {
            'coloring' => 'mulai Coloring',
            'final'    => 'tahap Final',
            default    => 'tahap berikutnya',
        };
        self::send(
            $artistId,
            "👍 {$clientName} menyetujui hasil kerjamu. Lanjutkan ke {$nextLabel}!",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Client minta revisi → notif ke ARTIST */
    public static function revisionRequested(
        string $artistId,
        string $orderId,
        string $clientName,
        int    $revisionCount,
        int    $revisionLimit
    ): void {
        self::send(
            $artistId,
            "🔄 {$clientName} meminta revisi (ke-{$revisionCount} dari {$revisionLimit}). Cek detail order.",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Order selesai → notif ke KEDUA PIHAK */
    public static function orderCompleted(
        string $clientId,
        string $artistId,
        string $orderId,
        string $serviceTitle
    ): void {
        self::send(
            $clientId,
            "🎉 Order \"{$serviceTitle}\" selesai! Jangan lupa beri review untuk artist.",
            'order',
            ['order_id' => $orderId]
        );
        self::send(
            $artistId,
            "🎉 Order \"{$serviceTitle}\" telah selesai dan disetujui client. Pembayaran akan diproses.",
            'order',
            ['order_id' => $orderId]
        );
    }

    /* ══════════════════════════════════════════
     |  EXTENSION EVENTS
    ══════════════════════════════════════════ */

    /** Artist minta tambah waktu → notif ke CLIENT */
    public static function extensionRequested(
        string $clientId,
        string $orderId,
        string $artistName,
        int    $days,
        string $reason
    ): void {
        self::send(
            $clientId,
            "⏳ {$artistName} meminta tambahan {$days} hari pengerjaan. Alasan: \"{$reason}\". Setujui atau tolak di detail order.",
            'order',
            ['order_id' => $orderId]
        );
    }

    /** Client setujui/tolak extension → notif ke ARTIST */
    public static function extensionResponded(
        string $artistId,
        string $orderId,
        string $clientName,
        string $response
    ): void {
        $msg = $response === 'approved'
            ? "✅ {$clientName} menyetujui perpanjangan waktu pengerjaan."
            : "❌ {$clientName} menolak permintaan perpanjangan waktu.";

        self::send($artistId, $msg, 'order', ['order_id' => $orderId]);
    }

    /* ══════════════════════════════════════════
     |  REVIEW EVENTS
    ══════════════════════════════════════════ */

    /** Review dikirim → notif ke pihak LAIN */
    public static function reviewSubmitted(
        string $targetUserId,
        string $orderId,
        string $reviewerName
    ): void {
        self::send(
            $targetUserId,
            "⭐ {$reviewerName} telah memberikan review. Akan tampil setelah kedua pihak submit atau 14 hari berlalu.",
            'review',
            ['order_id' => $orderId]
        );
    }

    /* ══════════════════════════════════════════
     |  LIKE EVENTS
    ══════════════════════════════════════════ */

    /** Someone liked artwork/service → notif ke PEMILIK */
    public static function likeReceived(
        string $ownerId,
        string $likerName,
        string $itemTitle,
        string $itemType,        // 'artwork' | 'commission'
        ?string $serviceId = null
    ): void {
        $label = $itemType === 'artwork' ? 'artwork' : 'jasa commission';
        self::send(
            $ownerId,
            "❤️ {$likerName} menyukai {$label} \"{$itemTitle}\" milikmu.",
            'like',
            $serviceId ? ['service_id' => $serviceId] : []
        );
    }
}
