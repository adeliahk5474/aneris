<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'is_verified',
        'country'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    // ── HELPER METHODS ──
    public function isVerifiedArtist(): bool
    {
        return $this->role === 'artist' && $this->is_verified;
    }

    public function canUploadCommission(): bool
    {
        return $this->isVerifiedArtist();
    }

    // Relasi ke verifikasi terbaru
    public function latestVerification()
    {
        return $this->hasOne(\App\Models\PortfolioVerification::class, 'artist_id', 'user_id')
            ->latest();
    }

    public function verifications()
    {
        return $this->hasMany(\App\Models\PortfolioVerification::class, 'artist_id', 'user_id');
    }

    // 👇 Tambahkan fungsi ini supaya UUID dibuat otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // USER YANG FOLLOW AKUN INI
    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_id',
            'follower_id',
            'user_id',
            'user_id'
        );
    }

    // Relationships
    // USER YANG DIA FOLLOW
    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'follower_id',
            'following_id',
            'user_id',
            'user_id'
        );
    }

    public function artworks()
    {
        return $this->hasMany(Artwork::class, 'user_id', 'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'client_id');
    }

    public function ordersAsClient()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function ordersAsArtist()
    {
        return $this->hasMany(Order::class, 'artist_id');
    }

    public function commissionRequestsAsClient()
    {
        return $this->hasMany(CommissionRequest::class, 'client_id');
    }

    public function commissionRequestsAsArtist()
    {
        return $this->hasMany(CommissionRequest::class, 'artist_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function commissionServices()
    {
        return $this->hasMany(\App\Models\CommissionService::class, 'artist_id', 'user_id');
    }
}
