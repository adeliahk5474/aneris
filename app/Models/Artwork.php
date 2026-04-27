<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artwork extends Model
{
    protected $primaryKey = 'artwork_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'category_id',
        'image_url',
        'caption',
        'status'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->artwork_id)) {
                $model->artwork_id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }



    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Compatibility accessors: some views expect `preview_url` or `file_url`
    public function getPreviewUrlAttribute()
    {
        return $this->image_url ?? null;
    }

    public function getFileUrlAttribute()
    {
        return $this->image_url ?? null;
    }
}
