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
            'url' => data_get($item->data, 'url'),
            'reload' => data_get($item->data, 'reload', false),
            'caption' => $item->type === 'image' ? $item->data['caption'] ?? null : null,
            'captionPosition' => $item->data['caption_position'] ?? 'below',
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
