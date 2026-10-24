@props(['screen', 'slideshows'])

@can('manager')
    <form method="POST" action="{{ route('screens.activate', $screen) }}">
        @csrf
        <x-slideshow-select :slideshows="$slideshows" :selected="$screen->slideshow_id" onchange="this.form.requestSubmit()" />
    </form>
@else
    <span @class([
        'italic',
        'text-mauve-200' => $screen->slideshow_id,
        'text-mauve-500' => !$screen->slideshow_id,
    ])>{{ $slideshows->firstWhere('id', $screen->slideshow_id)?->name ?? 'None' }}</span>
@endcan
