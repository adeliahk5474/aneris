<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'display_name',
        'bio',
        'profile_image',
        'banner_image',
        'instagram',
        'twitter',
        'tiktok',
        'website',
        'skills',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}
