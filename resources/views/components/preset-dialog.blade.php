@props(['id', 'screenGroups', 'slideshows', 'preset' => null])

<dialog id="{{ $id }}" closedby="any"
    class="m-auto w-full max-w-md bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <form method="POST" action="{{ $preset ? route('presets.update', $preset) : route('presets.store') }}"
        x-data="{
            effects: @js($preset?->data ?? []),
            defaultScreenGroup: @js($screenGroups->first()?->id),
            defaultSlideshow: @js($slideshows->first()?->id),
        }" class="space-y-3">
        @csrf
        <input type="hidden" name="data" x-bind:value="JSON.stringify(effects)">
        @if ($preset)
            @method('PUT')
        @endif

        <h2 class="text-lg font-bold text-mauve-50">
            {{ $preset ? 'Edit preset' : 'Create a new preset' }}</h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Name</dt>
            <dd><x-input name="name" value="{{ $preset?->name }}" required /></dd>
            <dt class="self-start text-mauve-400">Effects</dt>
            <dd class="max-h-60 overflow-y-auto">
                <template x-for="(effect, index) in effects">
                    <div class="flex items-center gap-2 py-0.5">
                        <x-select x-model.number="effect.screen_group_id">
                            @foreach ($screenGroups as $group)
                                <x-option value="{{ $group->id }}">{{ $group->name }}</x-option>
                            @endforeach
                        </x-select>
                        <x-select x-model.number="effect.slideshow_id">
                            @foreach ($slideshows as $slideshow)
                                <x-option value="{{ $slideshow->id }}">{{ $slideshow->name }}</x-option>
                            @endforeach
                        </x-select>
                        <x-button variant="muted" x-on:click="effects.splice(index, 1)">Remove</x-button>
                    </div>
                </template>
                <span class="text-mauve-500 italic" x-show="effects.length == 0">No effects yet</span>
                <div class="w-full flex justify-end">
                    <x-button variant="ghost"
                        x-on:click="effects.push({ screen_group_id: defaultScreenGroup, slideshow_id: defaultSlideshow })">Add
                        Effect</x-button>
                </div>
            </dd>
        </dl>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button type="submit">{{ $preset ? 'Save' : 'Create' }}</x-button>
        </div>
    </form>
</dialog>
