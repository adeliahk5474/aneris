<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;
    protected $fillable = ['artist_id', 'title', 'description', 'image_path'];

    public function artist() {
        return $this->belongsTo(Artist::class);
    }
}
