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
        $media = Media::findMany($this->items->pluck('data.media')->filter())->keyBy('id');

        return $this->items->sortBy('id')->map(function (SlideshowItem $item) use ($media) {
            $file = $item->type === 'media' ? $media->get($item->data['media']) : null;

            return [
                'type' => $item->type,
                'duration' => $item->duration ?? $file?->duration,
                'src' => $file ? route('media.show', $file, false) : null,
                'video' => $file?->type === 'video',
                'loop' => $item->duration !== null,
                'url' => data_get($item->data, 'url'),
                'reload' => data_get($item->data, 'reload', false),
                'caption' => $file ? $item->data['caption'] ?? null : null,
                'captionPosition' => $item->data['caption_position'] ?? 'below',
            ];
        })->values()->all();
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
