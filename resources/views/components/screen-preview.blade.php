@props(['src'])

{{-- Render the preview at 1080p to more closely replicate real display conditions --}}
{{-- Otherwise embedded web content scales to the tiny size, and doesn't look like it would on a TV --}}
<div {{ $attributes->class('aspect-video overflow-hidden bg-black [container-type:inline-size]') }}>
    <iframe src="{{ $src }}" loading="lazy" class="block origin-top-left border-0"
        style="--w: 1920px; width: var(--w); height: 1080px; scale: tan(atan2(100cqw, var(--w)));"></iframe>
</div>
