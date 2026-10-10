@props(['id', 'screens', 'screenGroup' => null])

<dialog id="{{ $id }}" closedby="any" class="m-auto w-full max-w-sm bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <form method="POST" action="{{ $screenGroup ? route('screen-groups.update', $screenGroup) : route('screen-groups.store') }}" class="space-y-3">
        @csrf
        @if ($screenGroup)
            @method('PUT')
        @endif

        <h2 class="text-lg font-bold text-mauve-50">{{ $screenGroup ? 'Edit screen group' : 'Create a new screen group' }}</h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Name</dt>
            <dd><x-input name="name" value="{{ $screenGroup?->name }}" required /></dd>
            <dt class="self-start text-mauve-400">Screens</dt>
            <dd class="max-h-60 overflow-y-auto">
                @forelse ($screens as $screen)
                    <label class="flex cursor-pointer items-center gap-2 py-0.5">
                        <input
                            type="checkbox"
                            name="screens[]"
                            value="{{ $screen->id }}"
                            class="accent-mauve-500"
                            @checked($screenGroup?->screens->contains($screen))
                        >
                        <x-screen-label :screen="$screen" />
                    </label>
                @empty
                    <span class="text-mauve-500 italic">No screens yet</span>
                @endforelse
            </dd>
        </dl>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button type="submit">{{ $screenGroup ? 'Save' : 'Create' }}</x-button>
        </div>
    </form>
</dialog>
