@props(['id', 'slideshow' => null])

@php
    $sections = $slideshow
        ? $slideshow->items
            ->sortBy('id')
            ->map(
                fn($item) => [
                    'type' => $item->type,
                    'duration' => $item->duration,
                    'data' => ['image' => $item->data['image'] ?? null],
                ],
            )
            ->values()
        : [];
@endphp

<dialog id="{{ $id }}" closedby="any"
    class="mx-auto mt-auto w-full max-w-dvw md:max-w-[calc(100dvw-4rem)] h-[calc(100dvh-2rem)] bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <div class="space-y-3" x-data="{
        name: @js($slideshow?->name ?? ''),
        error: null,
        sections: @js($sections),
        endpoint: '{{ route('images.store') }}',
        move(index, offset) {
            this.sections.splice(index + offset, 0, ...this.sections.splice(index, 1));
        },
        async save() {
            this.error = null;
    
            try {
                const response = await fetch(@js($slideshow ? route('slideshows.update', $slideshow) : route('slideshows.store')), {
                    method: @js($slideshow ? 'PUT' : 'POST'),
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: JSON.stringify({ name: this.name, sections: this.sections }),
                });
    
                if (response.ok) {
                    window.location.reload();
                    return;
                }
    
                this.error = (await response.json()).message;
            } catch {
                this.error = 'Could not save the slideshow.';
            }
        },
    }">
        <dl class="flex justify-between">
            <h2 class="text-lg font-bold text-mauve-50">
                {{ $slideshow ? 'Edit TV screen' : 'Create a new TV screen' }}
            </h2>
            <x-button variant="ghost" commandfor="{{ $id }}" command="close"
                class="text-mauve-500">Close</x-button>
        </dl>

        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1 max-w-100">
            <dt class="text-mauve-400">Name</dt>
            <dd><x-input x-model="name" /></dd>
        </dl>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <template x-for="(section, index) in sections">
                <dl class="h-full bg-mauve-800 p-4">
                    <div class="grid grid-cols-[100px_1fr] gap-y-1 h-fit">
                        <dt class="col-span-2 flex gap-1 items-center">
                            <button type="button"
                                class="cursor-pointer text-mauve-400 hover:text-mauve-100 disabled:cursor-default disabled:opacity-30"
                                :disabled="index === sections.length - 1"
                                x-on:click="move(index, 1)"><x-lucide-chevron-down class="size-4" /></button>
                            <button type="button"
                                class="cursor-pointer text-mauve-400 hover:text-mauve-100 disabled:cursor-default disabled:opacity-30"
                                :disabled="index === 0" x-on:click="move(index, -1)"><x-lucide-chevron-up
                                    class="size-4" /></button>
                            <button type="button" class="ml-auto cursor-pointer text-mauve-400 hover:underline"
                                x-on:click="sections.splice(index, 1)">Remove</button>
                        </dt>
                        <dt class="text-mauve-400">Duration (s)</dt>
                        <dd><x-input x-model="section.duration" type="number" /></dd>
                        <dt class="text-mauve-400">Type</dt>
                        <dd>
                            <x-select x-model="section.type">
                                <x-option value="image">Image</x-option>
                                <x-option value="clock">Clock</x-option>
                            </x-select>
                        </dd>
                        <dt x-show="section.type == 'image'" class="text-mauve-400">Image</dt>
                        <dd x-show="section.type == 'image'" class="space-y-1">
                            <x-button variant="outline" class="w-full"
                                x-on:click="$dispatch('pick-image', (image) => section.data.image = image)">Select
                                image</x-button>
                            <template x-if="section.data.image">
                                <img :src="`${endpoint}/preview/${section.data.image}`" loading="lazy"
                                    class="max-h-40 border border-mauve-700">
                            </template>
                        </dd>
                    </div>
                </dl>
            </template>
        </div>

        <p x-show="error" x-text="error" class="border border-red-800 bg-red-950 px-2 py-1 text-red-300"></p>

        <div class="flex justify-end gap-2">
            <x-button variant="outline" class="mr-auto"
                x-on:click="sections.push({ type: 'image', duration: 5, data: { image: null }})">Add Section</x-button>

            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button x-on:click="save()">{{ $slideshow ? 'Save' : 'Create' }}</x-button>
        </div>
    </div>
</dialog>
