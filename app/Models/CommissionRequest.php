<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRequest extends Model
{
    protected $fillable = [
        'client_id', 'artist_id', 'category_id',
        'description', 'proposed_price', 'status'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'commission_request_id');
    }
}
