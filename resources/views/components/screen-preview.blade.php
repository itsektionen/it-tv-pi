@props(['src'])

{{-- Renders at 1920x1080 and scales down to the caller's width. tan(atan2(a, b)) divides two lengths. --}}
<div {{ $attributes->class('aspect-video overflow-hidden bg-black [container-type:inline-size]') }}>
    <iframe src="{{ $src }}" loading="lazy" class="block origin-top-left border-0"
        style="--w: 1920px; width: var(--w); height: 1080px; scale: tan(atan2(100cqw, var(--w)));"></iframe>
</div>
