@props(['screen'])

@if ($screen->name)
    <span class="text-mauve-50">{{ $screen->name }}</span>
@else
    <span class="font-mono text-mauve-400">#{{ $screen->id }}</span>
@endif
