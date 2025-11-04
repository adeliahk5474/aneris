<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'order_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    protected $primaryKey = 'chat_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // ===== RELATIONS =====
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
