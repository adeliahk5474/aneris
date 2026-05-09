<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';

    protected $primaryKey = 'follow_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'follow_id',
        'follower_id',
        'following_id',
    ];

    // ===============================
    // RELATIONS
    // ===============================

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id', 'user_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id', 'user_id');
    }
}
