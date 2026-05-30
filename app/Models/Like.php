<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Like extends Model
{
    protected $table = 'likes';

    protected $primaryKey = 'like_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'like_id',
        'user_id',
        'likeable_id',
        'likeable_type',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($like) {

            if (empty($like->like_id)) {
                $like->like_id = (string) Str::uuid();
            }
        });
    }

    /* ─────────────────────────────────────────────
     | RELATIONS
    ───────────────────────────────────────────── */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
