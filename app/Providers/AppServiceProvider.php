<?php

namespace App\Providers;

use App\Models\Screen;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Reverb\Events\ChannelCreated;
use Laravel\Reverb\Events\ChannelRemoved;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(ChannelCreated::class, fn(ChannelCreated $event) => Screen::setPresence($event->channel->name(), true));
        Event::listen(ChannelRemoved::class, fn(ChannelRemoved $event) => Screen::setPresence($event->channel->name(), false));
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            if ($event->command === 'reverb:start') {
                Screen::resetPresence();
            }
        });
    }
}
