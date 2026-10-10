<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable("type", "data", "duration", "slideshow_id")]
class SlideshowItem extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function slideshow(): BelongsTo
    {
        return $this->belongsTo(Slideshow::class);
    }
}
