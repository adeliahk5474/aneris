<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id',
        'service_id',
        'client_id',
        'artist_id',
        'note',
        'payment_method',
        'total_price',
        'status',
        'result_file'
    ];

    public function service()
    {
        return $this->belongsTo(CommissionService::class, 'service_id', 'service_id');
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'user_id');
    }

    public function artist()
    {
        return $this->belongsTo(User::class, 'artist_id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id');
    }
}
