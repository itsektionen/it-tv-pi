@props(['slideshow'])

<div {{ $attributes->class('flex gap-1 overflow-x-auto flex-wrap') }}>
    @foreach ($slideshow->items->sortBy('id') as $item)
        @if ($item->type === 'media')
            <img src="{{ route('media.showPreview', $item->data['media']) }}" alt="" loading="lazy"
                class="aspect-video w-40 shrink-0 border border-mauve-700 bg-black object-contain">
        @else
            <div
                class="flex aspect-video w-40 shrink-0 flex-col items-center justify-center gap-0.5 border border-mauve-700 bg-black px-1 text-mauve-200">
                <span>{{ ucfirst($item->type) }}</span>
                @if ($item->type === 'iframe')
                    <span class="max-w-full truncate text-xs text-mauve-500">{{ parse_url($item->data['url'], PHP_URL_HOST) }}</span>
                @endif
            </div>
        @endif
    @endforeach
</div>
