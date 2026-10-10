<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable("name")]
class Slideshow extends Model
{
    public static function clientState(): array
    {
        return [
            'startedAt' => Cache::get('slideshow.started_at', 0),
            'items' => static::where('active', true)->first()?->clientItems() ?? [],
        ];
    }

    public function clientItems(): array
    {
        return $this->items->sortBy('id')->map(fn(SlideshowItem $item) => [
            'type' => $item->type,
            'duration' => $item->duration,
            'image' => $item->type === 'image' ? route('images.show', $item->data['image'], false) : null,
        ])->values()->all();
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(SlideshowItem::class);
    }
}
