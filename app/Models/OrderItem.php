<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'artwork_id', 'file_result_url',
        'note', 'revision_request', 'final_delivery_date',
        'price'
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function artwork() {
        return $this->belongsTo(Artwork::class, 'artwork_id');
    }
}
