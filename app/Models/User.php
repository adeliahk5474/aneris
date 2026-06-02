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
        'country',
        'remember_token',
    ];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }
    public function isVerifiedArtist(): bool
    {
        return $this->role === 'artist' && $this->is_verified;
    }
    public function canUploadCommission(): bool
    {
        return $this->isVerifiedArtist();
    }
    public function latestVerification()
    {
        return $this->hasOne(\App\Models\PortfolioVerification::class, 'artist_id', 'user_id')
            ->latest();
    }
    public function verifications()
    {
        return $this->hasMany(\App\Models\PortfolioVerification::class, 'artist_id', 'user_id');
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
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
    public function ordersAsClient()
    {
        return $this->hasMany(Order::class, 'client_id');
    }
    public function ordersAsArtist()
    {
        return $this->hasMany(Order::class, 'artist_id');
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
