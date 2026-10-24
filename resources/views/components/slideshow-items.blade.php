@props(['slideshow'])

<div {{ $attributes->class('flex gap-1 overflow-x-auto') }}>
    @foreach ($slideshow->items->sortBy('id') as $item)
        @if ($item->type === 'image')
            <img src="{{ route('images.showPreview', $item->data['image']) }}" alt="" loading="lazy"
                class="aspect-video w-40 shrink-0 border border-mauve-700 bg-black object-contain">
        @else
            <div
                class="flex aspect-video w-40 shrink-0 items-center justify-center border border-mauve-700 bg-black text-mauve-200">
                {{ ucfirst($item->type) }}</div>
        @endif
    @endforeach
</div>
