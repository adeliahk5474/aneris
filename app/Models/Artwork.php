<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artwork extends Model
{
    protected $primaryKey = 'artwork_id';
    public $incrementing = false;   // Penting supaya Laravel tahu bukan auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'artist_id', 'category_id', 'title', 'description',
        'file_url', 'preview_url', 'price', 'status'
    ];

    // Auto-generate UUID sebelum create
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->artwork_id)) {
                $model->artwork_id = (string) Str::uuid();
            }
        });
    }

    public function artist() {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cartItems() {
        return $this->hasMany(CartItem::class, 'artwork_id');
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class, 'artwork_id');
    }

    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'artwork_id', // FK di order_items
            'order_id',   // FK di reviews
            'artwork_id', // PK di artworks
            'order_id'    // PK di order_items
        );
    }
}
