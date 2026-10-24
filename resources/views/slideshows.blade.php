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

        <table class="w-full hidden lg:table">
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
                            {{-- Update: it is bad but not at the scale of how it's used here in slideshows so I won't fix it --}}
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
                            @endcan
                            @can('admin')
                                <x-button variant="danger" commandfor="remove-dialog-{{ $s->id }}"
                                    command="show-modal">Remove</x-button>
                            @endcan
                        </td>
                    </tr>
                    <tr x-show="matches(@js($s->name))" class="slideshow-display">
                        <td colspan="4" class="px-2 pb-2">
                            <x-slideshow-items :slideshow="$s" />
                        </td>
                    </tr>
                @endforeach
                @if ($slideshows->isEmpty())
                    <tr>
                        <td colspan="4" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="space-y-2 lg:hidden">
            @foreach ($slideshows as $s)
                <div x-show="matches(@js($s->name))">
                    <x-table-card>
                        <dt>Name</dt>
                        <dd class="truncate text-mauve-50">{{ $s->name }}</dd>
                        <dt>Created At</dt>
                        <dd>{{ $s->created_at }}</dd>
                        <dt>Updated At</dt>
                        <dd>{{ $s->updated_at }}</dd>

                        <x-slot:actions>
                            <x-button variant="ghost"
                                popovertarget="card-preview-{{ $s->id }}">Preview</x-button>
                            <div id="card-preview-{{ $s->id }}" popover
                                class="m-auto border border-mauve-700 bg-black p-0 backdrop:bg-black/60">
                                <iframe src="{{ route('preview', $s) }}" loading="lazy"
                                    class="block aspect-video w-[min(30rem,calc(100vw-2rem))]"></iframe>
                            </div>
                            @can('manager')
                                <x-button variant="ghost" commandfor="edit-dialog-{{ $s->id }}"
                                    command="show-modal">Edit</x-button>
                            @endcan
                            @can('admin')
                                <x-button variant="danger" commandfor="remove-dialog-{{ $s->id }}"
                                    command="show-modal">Remove</x-button>
                            @endcan
                        </x-slot:actions>

                        <x-slot:footer>
                            <x-slideshow-items :slideshow="$s" />
                        </x-slot:footer>
                    </x-table-card>
                </div>
            @endforeach
            @if ($slideshows->isEmpty())
                <p class="px-2 py-1 text-mauve-500 italic">Nothing here yet</p>
            @endif
        </div>

        @foreach ($slideshows as $s)
            @can('manager')
                <x-slideshow-dialog :id="'edit-dialog-' . $s->id" :slideshow="$s" />
            @endcan
            @can('admin')
                <x-confirm-dialog :id="'remove-dialog-' . $s->id" :action="route('slideshows.destroy', $s)" method="DELETE">
                    Remove slideshow <span class="font-bold text-mauve-50">{{ $s->name }}</span>?
                </x-confirm-dialog>
            @endcan
        @endforeach
    </div>
</x-layout>
