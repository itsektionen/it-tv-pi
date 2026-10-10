<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable("name")]
class Slideshow extends Model
{
    public function clientItems(): array
    {
        return $this->items->sortBy('id')->map(fn(SlideshowItem $item) => [
            'type' => $item->type,
            'duration' => $item->duration,
            'image' => $item->type === 'image' ? route('images.show', $item->data['image'], false) : null,
        ])->values()->all();
    }

    public function items(): HasMany
    {
        return $this->hasMany(SlideshowItem::class);
    }

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }
}
