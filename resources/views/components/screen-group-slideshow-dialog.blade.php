@props(['id', 'screenGroup', 'slideshows'])

@php
    $current = $screenGroup->screens->pluck('slideshow_id')->unique();
@endphp

<dialog id="{{ $id }}" closedby="any" class="m-auto w-full max-w-sm bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <form method="POST" action="{{ route('screen-groups.activate', $screenGroup) }}" class="space-y-3">
        @csrf

        <h2 class="text-lg font-bold text-mauve-50">Set slideshow for <span class="italic">{{ $screenGroup->name }}</span></h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Slideshow</dt>
            <dd><x-slideshow-select :slideshows="$slideshows" :selected="$current->count() === 1 ? $current->first() : null" /></dd>
        </dl>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button type="submit">Activate</x-button>
        </div>
    </form>
</dialog>
