<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['order_id', 'reviewer_id', 'rating', 'comment'];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
