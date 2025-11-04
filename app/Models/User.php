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
        'name', 'email', 'password', 'role', 'avatar', 'bio', 'country'
    ];

    protected $hidden = ['password', 'remember_token'];

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

    // Relationships
    public function artworks() {
        return $this->hasMany(Artwork::class, 'artist_id');
    }

    public function carts() {
        return $this->hasMany(Cart::class, 'client_id');
    }

    public function ordersAsClient() {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function ordersAsArtist() {
        return $this->hasMany(Order::class, 'artist_id');
    }

    public function commissionRequestsAsClient() {
        return $this->hasMany(CommissionRequest::class, 'client_id');
    }

    public function commissionRequestsAsArtist() {
        return $this->hasMany(CommissionRequest::class, 'artist_id');
    }

    public function reviews() {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function notifications() {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function chats() {
        return $this->hasMany(Chat::class, 'sender_id');
    }
}
