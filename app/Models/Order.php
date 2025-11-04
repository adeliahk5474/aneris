<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id', 'artist_id', 'category_id', 'total_price', 'status'
    ];

    public function client() {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function artist() {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function transaction() {
        return $this->hasOne(Transaction::class, 'order_id');
    }
}
