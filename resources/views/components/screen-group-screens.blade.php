@props(['screenGroup'])

<div class="flex flex-wrap gap-x-1">
    @forelse ($screenGroup->screens as $screen)
        <span class="flex"><x-screen-label :screen="$screen" />
            @if (!$loop->last)
                <span>,</span>
            @endif
        </span>
    @empty
        <span class="text-mauve-500 italic">No screens</span>
    @endforelse
</div>
