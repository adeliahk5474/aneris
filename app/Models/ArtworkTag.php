<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArtworkTag extends Pivot
{
    protected $table = 'artwork_tag';
    protected $fillable = ['artwork_id', 'tag_id'];
}
