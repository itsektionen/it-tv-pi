@props(['onMediaClick' => null])

@php
    $folders = \App\Models\Folder::orderBy('name')->get();
    $tree = $folders->groupBy('parent_id');

    $folder = request()->query('folder');
    $selected = match (true) {
        $folder === 'uncategorized' => '',
        is_string($folder) && $folders->contains('id', (int) $folder) => (int) $folder,
        default => null,
    };

    $media = \App\Models\Media::orderBy('name')->get(['id', 'name', 'type', 'duration', 'folder_id']);
@endphp

<div x-data="{
    selected: @js($selected),
    parent: null,
    target: null,
    endpoint: @js(route('media.store')),
    folders: @js($folders->keyBy('id')->map->only('name', 'parent_id')),
    media: @js($media),
    uploading: false,
    error: null,
    init() {
        this.$watch('selected', () => this.syncQuery());
    },
    matches(folder) {
        if (this.selected === null) {
            return true;
        }

        return folder === (this.selected === '' ? null : this.selected);
    },
    syncQuery() {
        const url = new URL(window.location);

        this.selected === null ?
            url.searchParams.delete('folder') :
            url.searchParams.set('folder', this.selected === '' ? 'uncategorized' : this.selected);

        history.replaceState(null, '', url);
    },
    path(id) {
        const folder = this.folders[id];

        return folder ? `${folder.parent_id ? this.path(folder.parent_id) : ''}/${folder.name}` : '/';
    },
    length(seconds) {
        seconds = Math.round(seconds);

        return `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;
    },
    async upload(file) {
        const body = new FormData();

        body.append('file', file);
        body.append('folder_id', this.selected || '');

        this.error = null;
        this.uploading = true;

        try {
            const response = await fetch(this.endpoint, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                body,
            });

            if (response.ok) {
                this.media.push(await response.json());
                this.media.sort((a, b) => a.name.localeCompare(b.name));
            } else {
                this.error = (await response.json()).message;
            }
        } catch {
            this.error = `Could not upload ${file.name}.`;
        }

        this.uploading = false;
    },
}">
    @can('manager')
        <div class="flex justify-end pb-1">
            <input type="file" accept="image/*,video/*" class="hidden" x-ref="upload"
                x-on:change="$el.files.length && upload($el.files[0]); $el.value = ''">
            <x-button variant="ghost" x-on:click="$refs.upload.click()" ::disabled="uploading"
                class="disabled:cursor-default disabled:opacity-50"
                x-text="uploading ? 'Uploading...' : 'Upload Media'">Upload Media</x-button>
            <x-button variant="ghost" commandfor="create-folder-dialog" command="show-modal"
                x-on:click="parent = null">Create Folder</x-button>
        </div>

        <p x-show="error" x-text="error" class="mb-2 border border-red-800 bg-red-950 px-2 py-1 text-red-300"></p>

        <x-folder-dialog id="create-folder-dialog" />
    @endcan

    @can('admin')
        <x-media-dialog id="edit-media-dialog" :folders="$folders" />
        <x-confirm-dialog id="remove-media-dialog" action="" x-bind:action="target && `${endpoint}/${target.id}`"
            method="DELETE">
            Remove <span class="font-bold text-mauve-50" x-text="target?.name"></span>?
        </x-confirm-dialog>
    @endcan

    <div class="flex gap-4">
        <aside class="w-48 shrink-0">
            <button type="button" class="w-full px-2 py-0.5 text-left"
                :class="selected === null ? 'bg-mauve-700 text-mauve-50' : 'text-mauve-400 hover:underline'"
                x-on:click="selected = null">All media</button>
            <x-folder-tree :folders="$tree" />
            <div class="mt-2 border-t border-dashed border-mauve-700 pt-2">
                <button type="button" class="w-full px-2 py-0.5 text-left italic"
                    :class="selected === '' ? 'bg-mauve-800 text-mauve-50' : 'text-mauve-500 hover:underline'"
                    x-on:click="selected = ''">Uncategorized</button>
            </div>
        </aside>

        <div class="grid flex-1 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 content-start gap-2">
            <template x-for="item in media" :key="item.id">
                <figure x-show="matches(item.folder_id)">
                    @if ($onMediaClick)
                        <button type="button" class="block w-full cursor-pointer" :title="`Select ${item.name}`"
                            x-on:click="{{ $onMediaClick }}">
                    @endif
                    <div class="relative">
                        <img :src="`${endpoint}/preview/${item.id}`" :alt="item.name" loading="lazy"
                            @class([
                                'aspect-video w-full border border-mauve-700 bg-black object-contain',
                                'hover:border-mauve-400' => $onMediaClick,
                            ])>
                        <span x-show="item.type === 'video'"
                            class="absolute right-1 bottom-1 flex items-center gap-1 bg-black/70 px-1 text-xs text-mauve-50">
                            <x-lucide-play class="size-3" />
                            <span x-show="item.duration" x-text="length(item.duration)"></span>
                        </span>
                    </div>
                    @if ($onMediaClick)
                        </button>
                    @endif
                    <figcaption class="flex items-center gap-1 px-1 text-mauve-200 italic">
                        <a class="min-w-0 flex-1 truncate" :title="item.name" x-text="item.name"></a>

                        @can('admin')
                            <button type="button" class="cursor-pointer" title="Edit" commandfor="edit-media-dialog"
                                command="show-modal" x-on:click="target = item">
                                <x-lucide-pen class="size-4 shrink-0 stroke-mauve-500 hover:stroke-mauve-100" />
                            </button>

                            <button type="button" class="cursor-pointer" title="Remove" commandfor="remove-media-dialog"
                                command="show-modal" x-on:click="target = item">
                                <x-lucide-trash class="size-4 shrink-0 stroke-mauve-500 hover:stroke-red-400" />
                            </button>
                        @endcan
                    </figcaption>
                </figure>
            </template>
            <p x-show="!media.some((item) => matches(item.folder_id))"
                class="col-span-4 px-2 py-1 text-mauve-500 italic">Nothing here yet</p>
        </div>
    </div>
</div>
