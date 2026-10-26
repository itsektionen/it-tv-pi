<?php

namespace App\Models;

use App\Events\ActiveSlideshowChanged;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable("name")]
#[Table(keyType: "string", incrementing: false)]
class Screen extends Model
{
    public static function connect(string $id): static
    {
        return static::findOr($id, fn() => tap((new static())->forceFill(['id' => $id]))->save());
    }

    public static function setPresence(string $channel, bool $active): void
    {
        if (!Str::startsWith($channel, 'screen.')) {
            return;
        }

        static::withoutTimestamps(fn() => static::whereRaw("replace(id, ':', '_') = ?", [Str::after($channel, 'screen.')])
            ->update(['active' => $active, 'last_active_at' => now()]));
    }

    public static function resetPresence(): void
    {
        static::withoutTimestamps(fn() => static::where('active', true)->update(['active' => false]));
    }

    public static function showSlideshow(Collection $screens, ?Slideshow $slideshow): void
    {
        if ($screens->isEmpty()) {
            return;
        }

        $startedAt = now()->startOfSecond();

        static::withoutTimestamps(fn() => static::whereKey($screens->modelKeys())
            ->update(['slideshow_id' => $slideshow?->id, 'slideshow_started_at' => $startedAt]));

        rescue(fn() => ActiveSlideshowChanged::dispatch($screens, [
            'startedAt' => $startedAt->getTimestampMs(),
            'items' => $slideshow?->clientItems() ?? [],
        ]));
    }

    public function clientState(): array
    {
        return [
            'startedAt' => $this->slideshow_started_at?->getTimestampMs() ?? 0,
            'items' => $this->slideshow?->clientItems() ?? [],
        ];
    }

    public function channel(): string
    {
        return 'screen.' . str_replace(':', '_', $this->id);
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'last_active_at' => 'datetime',
            'slideshow_started_at' => 'datetime',
        ];
    }

    public function slideshow(): BelongsTo
    {
        return $this->belongsTo(Slideshow::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ScreenGroup::class);
    }
}
