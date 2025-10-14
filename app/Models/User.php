<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'role',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // --- Relasi ---
    public function artist()
    {
        return $this->hasOne(Artist::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    // --- Accessor / Mutator ---
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    // --- Helper Role ---
    public function isArtist(): bool
    {
        return $this->role === 'artist';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}
