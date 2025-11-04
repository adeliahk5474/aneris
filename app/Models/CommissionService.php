<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommissionService extends Model
{
    protected $primaryKey = 'service_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'artist_id', 'category_id', 'title', 'description', 'price', 'status'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->service_id)) {
                $model->service_id = (string) Str::uuid();
            }
        });
    }

    public function artist() {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
