<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable("name", "parent_id")]
class Folder extends Model
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
