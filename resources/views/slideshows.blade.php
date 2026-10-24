<x-layout>
    @php
        $slideshows = \App\Models\Slideshow::with('items')->orderByDesc('id')->get();
    @endphp

    <div x-data="{
        search: @js(request('search', '')),
        init() {
            this.$watch('search', () => this.syncQuery());
        },
        syncQuery() {
            const url = new URL(window.location);
    
            this.search ? url.searchParams.set('search', this.search) : url.searchParams.delete('search');
    
            history.replaceState(null, '', url);
        },
        matches(name) {
            return name.toLowerCase().includes(this.search.toLowerCase());
        },
    }">
        <div class="mb-2 flex gap-2 items-center">
            <x-input x-model="search" type="search" placeholder="Search by name" class="max-w-xs" />

            @can('manager')
                <x-button class="ml-auto" variant="ghost" commandfor="create-dialog" command="show-modal">Create
                    Slideshow</x-button>
                <x-slideshow-dialog id="create-dialog" />
                <x-image-picker-dialog id="image-picker-dialog" />
            @endcan
        </div>

        <table class="w-full">
            <thead>
                <tr class="border-b border-mauve-700 text-left text-mauve-50">
                    <th class="px-2 font-bold">Name</th>
                    <th class="px-2 font-bold">Created At</th>
                    <th class="px-2 font-bold">Updated At</th>
                    <th class="px-2 font-bold w-25"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($slideshows as $s)
                    <tr x-show="matches(@js($s->name))">
                        <td class="px-2 text-mauve-200 italic">{{ $s->name }}</td>
                        <td class="px-2 text-mauve-200 italic">{{ $s->created_at }}</td>
                        <td class="px-2 text-mauve-200 italic">{{ $s->updated_at }}</td>
                        <td class="flex justify-end">
                            {{-- TODO: Investigate wether this is bad for hundreds of slideshows eg. having a lot of hidden previews.
                             Maybe we'd wanna consider having a persistent preview that is moved around but i think html handles this decently.  --}}
                            <x-button variant="ghost" popovertarget="preview-{{ $s->id }}"
                                style="anchor-name: --preview-{{ $s->id }}">Preview</x-button>
                            <div id="preview-{{ $s->id }}" popover
                                class="inset-auto m-0 mt-1 border border-mauve-700 bg-black p-0"
                                style="position-anchor: --preview-{{ $s->id }}; position-area: bottom span-left;">
                                <iframe src="{{ route('preview', $s) }}" loading="lazy"
                                    class="block aspect-video w-120"></iframe>
                            </div>
                            {{-- TODO: Investigate the same here --..-- --}}
                            @can('manager')
                                <x-button variant="ghost" commandfor="edit-dialog-{{ $s->id }}"
                                    command="show-modal">Edit</x-button>
                                <x-slideshow-dialog :id="'edit-dialog-' . $s->id" :slideshow="$s" />
                            @endcan
                            @can('admin')
                                <x-button variant="danger" commandfor="remove-dialog-{{ $s->id }}"
                                    command="show-modal">Remove</x-button>
                                <x-confirm-dialog :id="'remove-dialog-' . $s->id" :action="route('slideshows.destroy', $s)" method="DELETE">
                                    Remove slideshow <span class="font-bold text-mauve-50">{{ $s->name }}</span>?
                                </x-confirm-dialog>
                            @endcan
                        </td>
                    </tr>
                    <tr x-show="matches(@js($s->name))" class="slideshow-display">
                        <td colspan="4" class="px-2 pb-2">
                            <div class="flex gap-1 overflow-x-auto">
                                @foreach ($s->items->sortBy('id') as $item)
                                    @if ($item->type === 'image')
                                        <img src="{{ route('images.showPreview', $item->data['image']) }}"
                                            alt="" loading="lazy"
                                            class="aspect-video w-40 shrink-0 border border-mauve-700 bg-black object-contain">
                                    @else
                                        <div
                                            class="flex aspect-video w-40 shrink-0 items-center justify-center border border-mauve-700 bg-black text-mauve-200">
                                            {{ ucfirst($item->type) }}</div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if ($slideshows->isEmpty())
                    <tr>
                        <td colspan="5" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-layout>
