<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    public    $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'service_id',
        'client_id',
        'artist_id',
        'note',
        'payment_method',
        'payment_status',
        'selected_addons',
        'subtotal_price',
        'total_price',
        'status',
        'result_file',
        'final_file',
        'phase',
        'revision_count',
        'revision_limit',
        'deadline_at',
        'late_status',
        'extension_requested_at',
        'extension_reason',
        'extension_days',
        'extension_status',
        'paid_at',
        'snap_token',
    ];

    protected $casts = [
        'selected_addons'        => 'array',
        'total_price'            => 'float',
        'subtotal_price'         => 'float',
        'revision_count'         => 'integer',
        'revision_limit'         => 'integer',
        'deadline_at'            => 'datetime',
        'extension_requested_at' => 'datetime',
        'paid_at'                => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->order_id)) {
                $model->order_id = (string) Str::uuid();
            }
        });
    }

    // ── RELATIONS ──────────────────────────────────────────────────────

    public function service()
    {
        return $this->belongsTo(CommissionService::class, 'service_id', 'service_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'user_id');
    }

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id', 'order_id');
    }

    public function clientReview()
    {
        return $this->hasOne(Review::class, 'order_id', 'order_id')
            ->where('reviewer_type', 'client');
    }

    public function artistReview()
    {
        return $this->hasOne(Review::class, 'order_id', 'order_id')
            ->where('reviewer_type', 'artist');
    }

    // ── DEADLINE HELPERS ────────────────────────────────────────────────

    /**
     * Set deadline saat order jadi in_progress.
     * Dipanggil dari OrderController::accept() ketika artist terima order.
     */
    public function setDeadline(): void
    {
        $days = $this->service->estimated_days ?? 7;
        $this->update(['deadline_at' => now()->addDays($days)]);
    }

    /**
     * Cek dan update late_status berdasarkan deadline.
     * Dipanggil dari scheduled command atau saat order diakses.
     */
    public function refreshLateStatus(): void
    {
        if (!$this->deadline_at) return;
        if (in_array($this->status, ['completed', 'canceled'])) return;

        $diffHours = now()->diffInHours($this->deadline_at, false); // negatif = sudah lewat

        $new = match (true) {
            $diffHours >= 0   => null,        // masih on time
            $diffHours >= -24 => 'late',      // < 24 jam terlambat
            $diffHours >= -72 => 'overdue',   // 24-72 jam
            default           => 'delayed',   // > 72 jam
        };

        if ($this->late_status !== $new) {
            $this->update(['late_status' => $new]);
        }
    }

    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->deadline_at) return null;
        if (in_array($this->status, ['completed', 'canceled'])) return null;

        return now()->gt($this->deadline_at)
            ? 'Terlambat ' . $this->deadline_at->diffForHumans(now(), true)
            : $this->deadline_at->diffForHumans(now(), true) . ' tersisa';
    }

    public function isLate(): bool
    {
        return !is_null($this->late_status);
    }

    // ── REVISION HELPERS ────────────────────────────────────────────────

    public function revisionsLeft(): int
    {
        return max(0, ($this->revision_limit ?? 2) - ($this->revision_count ?? 0));
    }

    public function canRevise(): bool
    {
        return $this->revisionsLeft() > 0;
    }

    // ── EXTENSION HELPERS ────────────────────────────────────────────────

    public function hasExtensionRequest(): bool
    {
        return !is_null($this->extension_requested_at);
    }

    public function extensionPending(): bool
    {
        return $this->extension_status === 'pending';
    }

    // ── REVIEW HELPERS ──────────────────────────────────────────────────

    public function bothReviewsSubmitted(): bool
    {
        return $this->reviews()->count() >= 2;
    }

    public function hasClientReview(): bool
    {
        return $this->reviews()->where('reviewer_type', 'client')->exists();
    }

    public function hasArtistReview(): bool
    {
        return $this->reviews()->where('reviewer_type', 'artist')->exists();
    }
}
