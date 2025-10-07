<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;
    protected $fillable = [
        'artist_id', 'client_id', 'category_id', 'title', 'description', 'price', 'status', 'deadline'
    ];

    public function artist() {
        return $this->belongsTo(Artist::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}