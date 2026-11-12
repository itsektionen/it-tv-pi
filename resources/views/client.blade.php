@php
    $error ??= null;
    $screen ??= null;
    $state ??= $screen?->clientState() ?? ['items' => [], 'startedAt' => 0];
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
    @if ($error)
        @php
            dd($error);
        @endphp
        <div class="flex justify-center py-4"></div>
    @else
        <div class="relative size-full" x-data="{
            items: @js($state['items']),
            startedAt: @js($state['startedAt']),
            offset: @js(now()->getTimestampMs()) - Date.now(),
            current: 0,
            visits: {},
            loaded: {},
            now: new Date(),
            init() {
                @if ($screen) window.Echo.channel(@js($screen->channel())).listen('ActiveSlideshowChanged', (state) => {
                            this.items = state.items;
                            this.startedAt = state.startedAt;
                        }); @endif

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

                const index = this.items.findIndex((item) => (elapsed -= item.duration * 1000) < 0);
                if (index !== this.current) {
                    this.current = index;
                    if (!this.isKept(index)) {
                        this.visits[index] = (this.visits[index] ?? 0) + 1;
                    }
                    this.loaded = Object.fromEntries(
                        Object.entries(this.loaded).filter(([i]) => this.isKept(+i) || (+i !== index && this.shown(+i))),
                    );
                }
            },
            isKept(index) {
                return this.items[index]?.reload === false;
            },
            get displayed() {
                const count = this.items.length;
                const ready = this.items[this.current]?.type !== 'iframe' || this.loaded[this.current];
                return ready ? this.current : (this.current - 1 + count) % count;
            },
            shown(index) {
                const count = this.items.length;

                return [-1, 0].some((step) => ((this.current + step) % count + count) % count === index);
            },
            upcoming(index) {
                return !this.shown(index) && (this.current + 1) % this.items.length === index;
            },
        }">
            <template x-for="(item, index) in items">
                <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-1000"
                    :class="displayed === index ? 'opacity-100' : 'opacity-0'">
                    <template x-if="item.type === 'image' && upcoming(index)">
                        <link rel="preload" as="image" :href="item.image">
                    </template>
                    <template x-if="shown(index) || (isKept(index) && loaded[index])">
                        <div class="contents">
                            <template x-if="item.type === 'image' && !item.caption">
                                <img :src="item.image" class="size-full object-contain">
                            </template>
                            <template x-if="item.type === 'image' && item.caption">
                                <div class="grid size-full grid-cols-1 bg-black p-[5vh]"
                                    :class="item.captionPosition === 'above' ? 'grid-rows-[auto_minmax(0,1fr)]' : 'grid-rows-[minmax(0,1fr)_auto]'">
                                    <div class="flex items-center justify-center [container-type:size]">
                                        <img :src="item.image"
                                            x-on:load="$el.style.setProperty('--ratio', $el.naturalWidth / $el.naturalHeight)"
                                            class="box-content border-[0.4vh] border-white p-[0.8vh] w-[min(100cqw-2.4vh,(100cqh-2.4vh)*var(--ratio,1))]">
                                    </div>
                                    <p class="text-center font-serif text-[6vh] leading-tight text-white"
                                        :class="item.captionPosition === 'above' ? 'order-first pb-[3vh]' : 'pt-[3vh]'"
                                        x-text="item.caption"></p>
                                </div>
                            </template>
                            <template x-if="item.type === 'iframe' && /^https?:/i.test(item.url) && (index === current || loaded[index])">
                                {{-- Key on visit count makes it refresh every time, unless set to keep --}}
                                <template x-for="key in [visits[index] ?? 0]" :key="key">
                                    <iframe :src="item.url" @load="loaded[index] = true"
                                        sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                                        referrerpolicy="strict-origin-when-cross-origin"
                                        allow="autoplay; fullscreen"
                                        scrolling="no"
                                        class="pointer-events-none size-full border-0 bg-black"></iframe>
                                </template>
                            </template>
                            <template x-if="item.type === 'clock'">
                                <div class="w-fit flex items-center flex-col">
                                    <div class="flex justify-between text-[11vw] leading-none text-mauve-300 tabular-nums"
                                        x-text="now.toLocaleDateString('sv-SE', { timeZone: 'Europe/Stockholm' })">
                                    </div>
                                    <div class="text-[30vw] leading-none font-bold tabular-nums"
                                        x-text="now.toLocaleTimeString('sv-SE', { hour: '2-digit', minute: '2-digit', timeZone: 'Europe/Stockholm' })">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="!items.length">
                <div class="flex size-full items-center justify-center text-mauve-500">No active slideshow</div>
            </template>
        </div>
    @endif
</body>

</html>
