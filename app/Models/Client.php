<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_picture',
        'total_orders',
        'total_spent',
        'is_verified',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Commission (pesanan yang dibuat client)
    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
    public function cart() {
    return $this->hasOne(Cart::class);
}

}
