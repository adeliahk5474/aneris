<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'commission_id',
        'status',
        'total_price',
        'deadline',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
