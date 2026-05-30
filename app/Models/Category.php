<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function artworks()
    {
        return $this->hasMany(Artwork::class, 'category_id');
    }

    public function commissionRequests()
    {
        return $this->hasMany(CommissionRequest::class, 'category_id');
    }
}
