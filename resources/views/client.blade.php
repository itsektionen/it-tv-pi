@php
    $state ??= \App\Models\Slideshow::clientState();
    $live ??= true;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scheme-dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ITTV π</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="h-screen cursor-none overflow-hidden bg-black text-mauve-50">
        <div class="relative size-full" x-data="{
            items: @js($state['items']),
            startedAt: @js($state['startedAt']),
            offset: @js(now()->getTimestampMs()) - Date.now(),
            current: 0,
            now: new Date(),
            init() {
                if (@js($live)) {
                    window.Echo.channel('slideshow').listen('ActiveSlideshowChanged', (state) => {
                        this.items = state.items;
                        this.startedAt = state.startedAt;
                    });
                }

                this.tick();
                setInterval(() => this.tick(), 100);
            },
            tick() {
                const time = Date.now() + this.offset;
                const total = this.items.reduce((sum, item) => sum + item.duration * 1000, 0);

                this.now = new Date(time);

                if (!total) {
                    return;
                }

                let elapsed = ((time - this.startedAt) % total + total) % total;

                this.current = this.items.findIndex((item) => (elapsed -= item.duration * 1000) < 0);
            },
        }">
            <template x-for="(item, index) in items">
                <div
                    class="absolute inset-0 flex items-center justify-center transition-opacity duration-1000"
                    :class="current === index ? 'opacity-100' : 'opacity-0'"
                >
                    <template x-if="item.type === 'image'">
                        <img :src="item.image" class="size-full object-contain">
                    </template>
                    <template x-if="item.type === 'clock'">
                        <div class="w-fit flex items-center flex-col">
                            <div class="flex justify-between text-[11vw] leading-none text-mauve-300 tabular-nums" x-text="now.toLocaleDateString('sv-SE', { timeZone: 'Europe/Stockholm' })"></div>
                            <div class="text-[30vw] leading-none font-bold tabular-nums" x-text="now.toLocaleTimeString('sv-SE', { hour: '2-digit', minute: '2-digit', timeZone: 'Europe/Stockholm' })"></div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="! items.length">
                <div class="flex size-full items-center justify-center text-mauve-500">No active slideshow</div>
            </template>
        </div>
    </body>
</html>
