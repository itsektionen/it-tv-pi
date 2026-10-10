<?php

namespace App\Events;

use App\Models\Screen;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;

class ActiveSlideshowChanged implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(public Collection $screens, public array $state)
    {
    }

    public function broadcastOn(): array
    {
        return $this->screens->map(fn(Screen $screen) => new Channel($screen->channel()))->all();
    }

    public function broadcastWith(): array
    {
        return $this->state;
    }
}
