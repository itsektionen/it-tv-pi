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
            fading: false,
            preloadIdx: null,
            loadMs: {},
            fadeMs: 1100,
            defaultLoadMs: 3000,
            loadPaddingMs: 500,
            minLeadMs: 1000,
            maxLeadMs: 10000,
            now: new Date(),
            init() {
                @if ($screen) window.Echo.channel(@js($screen->channel())).listen('ActiveSlideshowChanged', (state) => {
                            this.items = state.items;
                            this.startedAt = state.startedAt;
                            this.preloadIdx = null;
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

                const elapsed = ((time - this.startedAt) % total + total) % total;

                const previous = this.current;
                let slideEnd = 0;
                this.current = this.items.findIndex((item) => (slideEnd += item.duration * 1000) > elapsed);
                const untilNext = slideEnd - elapsed;

                const duration = this.items[this.current].duration * 1000;
                this.fading = duration - untilNext < this.fadeMs;

                if (this.current !== previous) {
                    this.preloadIdx = null;
                }

                const next = this.neighbour(1);
                if (untilNext <= this.lead(this.items[next])) {
                    this.preloadIdx = next;
                }
            },
            neighbour(step) {
                const count = this.items.length;

                return ((this.current + step) % count + count) % count;
            },
            lead(item) {
                const loadMs = this.loadMs[item.url] ?? this.defaultLoadMs;

                return Math.min(Math.max(loadMs + this.loadPaddingMs, this.minLeadMs), this.maxLeadMs);
            },
            visible(index) {
                return index === this.current || (this.fading && index === this.neighbour(-1));
            },
            preloading(index) {
                return index === this.preloadIdx;
            },
            needsFrame(index) {
                const item = this.items[index];

                return item.type === 'iframe' && /^https?:\/\//i.test(item.url)
                    && (this.visible(index) || this.preloading(index));
            },
            parkedStyle(index) {
                return this.preloading(index) && !this.visible(index) ? 'transform: translateX(-200vw)' : '';
            },
            isBlankFrame(el) {
                try {
                    return el.contentWindow.location.href === 'about:blank';
                } catch {
                    return false;
                }
            },
            measured(el, item) {
                if (this.isBlankFrame(el)) {
                    return;
                }

                this.loadMs[item.url] = Date.now() - el._mountedAt;
            },
            shown(index) {
                return [-1, 0].some((step) => this.neighbour(step) === index);
            },
            upcoming(index) {
                return !this.shown(index) && this.neighbour(1) === index;
            },
        }">
            <template x-for="(item, index) in items">
                <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-1000"
                    :class="current === index ? 'opacity-100' : 'opacity-0'"
                    :style="parkedStyle(index)">
                    <template x-if="item.type === 'image' && upcoming(index)">
                        <link rel="preload" as="image" :href="item.image">
                    </template>
                    <template x-if="shown(index) || needsFrame(index)">
                        <div class="contents">
                            <template x-if="item.type === 'image'">
                                <img :src="item.image" class="size-full object-contain">
                            </template>
                            <template x-if="needsFrame(index)">
                                <iframe
                                    x-init="$el._mountedAt = Date.now()"
                                    @load="measured($el, item)"
                                    :src="item.url"
                                    sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                                    referrerpolicy="strict-origin-when-cross-origin"
                                    allow="autoplay; fullscreen"
                                    scrolling="no"
                                    class="pointer-events-none size-full border-0 bg-black"></iframe>
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
