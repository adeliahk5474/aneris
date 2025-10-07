<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'commission_id', 'client_id', 'artist_id', 'rating', 'comment'
    ];

    public function commission() {
        return $this->belongsTo(Commission::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function artist() {
        return $this->belongsTo(Artist::class);
    }
}

