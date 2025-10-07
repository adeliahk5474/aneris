<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'bio', 'profile_picture'];

    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'user');
    }
}