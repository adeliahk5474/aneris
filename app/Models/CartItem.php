<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model {
    protected $fillable = ['cart_id', 'artist_id', 'category_id', 'title', 'description', 'price', 'status'];

    public function cart() {
        return $this->belongsTo(Cart::class);
    }

    public function artist() {
        return $this->belongsTo(Artist::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}

