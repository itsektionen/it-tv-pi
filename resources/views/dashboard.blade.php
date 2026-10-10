<x-layout>
    <x-slot:actions>
        <x-button variant="ghost" commandfor="create-dialog" command="show-modal">Create Slideshow</x-button>
        <x-slideshow-dialog id="create-dialog" />
    </x-slot:actions>

    @php
        $slideshows = \App\Models\Slideshow::with(["items", "author"])->orderByDesc("id")->get();
        $authors = \App\Models\User::whereHas("slideshows")->orderBy("name")->get();
    @endphp

    <div x-data="{
        search: @js(request('search', '')),
        author: @js(request('author', '')),
        init() {
            this.$watch('search', () => this.syncQuery());
            this.$watch('author', () => this.syncQuery());
        },
        syncQuery() {
            const url = new URL(window.location);

            this.search ? url.searchParams.set('search', this.search) : url.searchParams.delete('search');
            this.author ? url.searchParams.set('author', this.author) : url.searchParams.delete('author');

            history.replaceState(null, '', url);
        },
        matches(name, author) {
            return name.toLowerCase().includes(this.search.toLowerCase())
                && (! this.author || this.author === String(author));
        },
    }">
    <div class="mb-2 flex gap-2">
        <x-input x-model="search" type="search" placeholder="Search by name" class="max-w-xs" />
        <x-select x-model="author" class="max-w-xs">
            <x-option value="">All authors</x-option>
            @foreach ($authors as $author)
                <x-option value="{{ $author->id }}">{{ $author->name }}</x-option>
            @endforeach
        </x-select>
    </div>

    <table class="w-full">
        <thead>
            <tr class="border-b border-mauve-700 text-left text-mauve-50">
                <th class="px-2 font-bold">#</th>
                <th class="px-2 font-bold">Name</th>
                <th class="px-2 font-bold">Author</th>
                <th class="px-2 font-bold">Created At</th>
                <th class="px-2 font-bold">Updated At</th>
                <th class="px-2 font-bold w-25"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($slideshows as $s)
                <tr x-show="matches(@js($s->name), @js($s->user_id))">
                    <td class="px-2 text-mauve-200 italic">{{ $s->id }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $s->name }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $s->author->name }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $s->created_at }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $s->updated_at }}</td>
                    <td class="flex justify-end">
                        @if ($s->active)
                            <span class="border border-transparent px-2 py-0.5 text-mauve-400">Active</span>
                        @else
                            <form method="POST" action="{{ route('slideshows.activate', $s) }}">
                                @csrf
                                <x-button variant="ghost" type="submit">Activate</x-button>
                            </form>
                        @endif
                        {{-- TODO: Investigate wether this is bad for hundreds of slideshows eg. having a lot of hidden previews. 
                             Maybe we'd wanna consider having a persistent preview that is moved around but i think html handles this decently.  --}}
                        <x-button variant="ghost" popovertarget="preview-{{ $s->id }}" style="anchor-name: --preview-{{ $s->id }}">Preview</x-button>
                        <div
                            id="preview-{{ $s->id }}"
                            popover
                            class="inset-auto m-0 mt-1 border border-mauve-700 bg-black p-0"
                            style="position-anchor: --preview-{{ $s->id }}; position-area: bottom span-left;"
                        >
                            <iframe src="{{ route('preview', $s) }}" loading="lazy" class="block aspect-video w-120"></iframe>
                        </div>
                        {{-- TODO: Investigate the same here --..-- --}}
                        <x-button variant="ghost" commandfor="edit-dialog-{{ $s->id }}" command="show-modal">Edit</x-button>
                        <x-slideshow-dialog :id="'edit-dialog-'.$s->id" :slideshow="$s" />
                        <x-button variant="danger" commandfor="remove-dialog-{{ $s->id }}" command="show-modal">Remove</x-button>
                        <x-confirm-dialog :id="'remove-dialog-'.$s->id" :action="route('slideshows.destroy', $s)" method="DELETE">
                            Remove slideshow <span class="font-bold text-mauve-50">{{ $s->name }}</span>?
                        </x-confirm-dialog>
                    </td>
                </tr>
            @endforeach
            @if ($slideshows->isEmpty())
                <tr>
                    <td colspan="6" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                </tr>
            @endif
        </tbody>
    </table>
    </div>
</x-layout>