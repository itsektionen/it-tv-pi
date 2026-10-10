<?php

namespace App\Events;

use App\Models\Slideshow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ActiveSlideshowChanged implements ShouldBroadcastNow
{
    use Dispatchable;

    public function broadcastOn(): array
    {
        return [new Channel('slideshow')];
    }

    public function broadcastWith(): array
    {
        return Slideshow::clientState();
    }
}
