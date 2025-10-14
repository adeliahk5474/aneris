<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'portfolio_link',
        'short_bio',
        'rating',
        'total_reviews',
        'is_verified',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Commission (jika sudah ada)
    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function profile()
{
    return $this->hasOne(ArtistProfile::class);
}

    // Hitung rata-rata rating
    public function getAverageRatingAttribute()
    {
        return $this->total_reviews > 0 ? round($this->rating / $this->total_reviews, 2) : null;
    }
    
}
