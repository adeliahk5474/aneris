<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'notif_id';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'notif_id',
        'user_id',
        'message',
        'type',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->notif_id)) {
                $model->notif_id = (string) Str::uuid();
            }
        });
    }

    /* ── RELATIONS ─────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /* ── ACCESSORS — dipakai di notifications.blade.php ── */

    public function getIsUnreadAttribute(): bool
    {
        return $this->status === 'unread';
    }

    public function getIconClassAttribute(): string
    {
        return match ($this->type) {
            'order'      => 'order',
            'commission' => 'commission',
            'review'     => 'review',
            'like'       => 'like',
            default      => 'system',
        };
    }

    public function getIconNameAttribute(): string
    {
        return match ($this->type) {
            'order'      => 'bi-bag-check',
            'commission' => 'bi-palette',
            'review'     => 'bi-star',
            'like'       => 'bi-heart',
            default      => 'bi-bell',
        };
    }

    public function getLinkUrlAttribute(): string
    {
        $meta = $this->metadata ?? [];

        if (!empty($meta['order_id'])) {
            return route('order.detail', $meta['order_id']);
        }
        if (!empty($meta['service_id'])) {
            return route('commission.show', $meta['service_id']);
        }

        return '#';
    }
}
