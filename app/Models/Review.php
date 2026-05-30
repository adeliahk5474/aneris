<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    protected $table      = 'reviews';
    protected $primaryKey = 'review_id';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'review_id',
        'order_id',
        'reviewer_id',
        'reviewer_type',
        'rating',
        'rating_quality',
        'rating_timeliness',
        'rating_communication',
        'rating_brief',
        'rating_attitude',
        'rating_revision',
        'comment',
        'is_visible',
        'revealed_at',
    ];

    protected $casts = [
        'rating'               => 'integer',
        'rating_quality'       => 'integer',
        'rating_timeliness'    => 'integer',
        'rating_communication' => 'integer',
        'rating_brief'         => 'integer',
        'rating_attitude'      => 'integer',
        'rating_revision'      => 'integer',
        'is_visible'           => 'boolean',
        'revealed_at'          => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->review_id)) {
                $model->review_id = (string) Str::uuid();
            }
        });
    }

    /* ── RELATIONS ─────────────────────────────── */

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'user_id');
    }

    /* ── HELPERS ───────────────────────────────── */

    /** Rata-rata sub-rating client → artist */
    public function clientAverageRating(): ?float
    {
        $ratings = array_filter([
            $this->rating_quality,
            $this->rating_timeliness,
            $this->rating_communication,
        ]);
        return count($ratings)
            ? round(array_sum($ratings) / count($ratings), 1)
            : null;
    }

    /** Rata-rata sub-rating artist → client */
    public function artistAverageRating(): ?float
    {
        $ratings = array_filter([
            $this->rating_brief,
            $this->rating_attitude,
            $this->rating_revision,
        ]);
        return count($ratings)
            ? round(array_sum($ratings) / count($ratings), 1)
            : null;
    }

    /** Cek apakah review sudah harus ditampilkan */
    public function shouldBeRevealed(): bool
    {
        if ($this->is_visible) return true;

        // Reveal otomatis setelah 14 hari
        if ($this->created_at->diffInDays(now()) >= 14) return true;

        // Reveal jika kedua pihak sudah submit
        if ($this->order) {
            $clientDone = $this->order->reviews()->where('reviewer_type', 'client')->exists();
            $artistDone = $this->order->reviews()->where('reviewer_type', 'artist')->exists();
            if ($clientDone && $artistDone) return true;
        }

        return false;
    }

    /** Reveal review ini dan pasangannya jika syarat terpenuhi */
    public function maybeReveal(): void
    {
        if ($this->is_visible || !$this->shouldBeRevealed()) return;

        $this->update([
            'is_visible'  => true,
            'revealed_at' => now(),
        ]);

        // Reveal juga review pasangan (dari pihak lain di order yang sama)
        if ($this->order) {
            $this->order->reviews()
                ->where('review_id', '!=', $this->review_id)
                ->where('is_visible', false)
                ->each(fn($r) => $r->update([
                    'is_visible'  => true,
                    'revealed_at' => now(),
                ]));
        }
    }
}
