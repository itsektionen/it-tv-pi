@props(['onImageClick' => null])

@php
    $folders = \App\Models\Folder::orderBy("name")->get();
    $tree = $folders->groupBy("parent_id");

    $folder = request()->query("folder");
    $selected = match (true) {
        $folder === "uncategorized" => "",
        is_string($folder) && $folders->contains("id", (int) $folder) => (int) $folder,
        default => null,
    };

    $images = \App\Models\Image::orderBy("name")->get();
@endphp

<div x-data="{
    selected: @js($selected),
    parent: null,
    folders: @js($folders->keyBy('id')->map->only('name', 'parent_id')),
    imageFolders: @js($images->pluck('folder_id')),
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

        this.selected === null
            ? url.searchParams.delete('folder')
            : url.searchParams.set('folder', this.selected === '' ? 'uncategorized' : this.selected);

        history.replaceState(null, '', url);
    },
    path(id) {
        const folder = this.folders[id];

        return folder ? `${folder.parent_id ? this.path(folder.parent_id) : ''}/${folder.name}` : '/';
    },
}">
    <div class="flex justify-end pb-1">
        <form method="POST" action="{{ route('images.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="folder_id" :value="selected || ''">
            <input type="file" name="image" accept="image/*" class="hidden" x-ref="upload" x-on:change="$el.form.requestSubmit()">
            <x-button variant="ghost" x-on:click="$refs.upload.click()">Upload Image</x-button>
        </form>
        <x-button variant="ghost" commandfor="create-folder-dialog" command="show-modal" x-on:click="parent = null">Create Folder</x-button>
    </div>

    <x-folder-dialog id="create-folder-dialog" />

    <div class="flex gap-4">
        <aside class="w-48 shrink-0">
            <button
                type="button"
                class="w-full px-2 py-0.5 text-left"
                :class="selected === null ? 'bg-mauve-700 text-mauve-50' : 'text-mauve-400 hover:underline'"
                x-on:click="selected = null"
            >All images</button>
            <x-folder-tree :folders="$tree" />
            <div class="mt-2 border-t border-dashed border-mauve-700 pt-2">
                <button
                    type="button"
                    class="w-full px-2 py-0.5 text-left italic"
                    :class="selected === '' ? 'bg-mauve-800 text-mauve-50' : 'text-mauve-500 hover:underline'"
                    x-on:click="selected = ''"
                >Uncategorized</button>
            </div>
        </aside>

        <div class="grid flex-1 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 content-start gap-2">
            @foreach ($images as $image)
                <figure x-show="matches(@js($image->folder_id))">
                    @if ($onImageClick)
                        <button type="button" class="block w-full cursor-pointer" title="Select {{ $image->name }}" x-data="{ image: @js($image->id) }" x-on:click="{{ $onImageClick }}">
                    @endif
                    <img src="{{ route('images.show', $image) }}" alt="{{ $image->name }}" loading="lazy" @class([
                        'aspect-video w-full border border-mauve-700 bg-black object-contain',
                        'hover:border-mauve-400' => $onImageClick,
                    ])>
                    @if ($onImageClick)
                        </button>
                    @endif
                    <figcaption class="flex items-center gap-1 px-1 text-mauve-200 italic">
                        <a class="min-w-0 flex-1 truncate" title="{{ $image->name }}">{{ $image->name }}</a>

                        <button type="button" class="cursor-pointer" title="Edit" commandfor="edit-image-dialog-{{ $image->id }}" command="show-modal">
                            <x-lucide-pen class="size-4 shrink-0 stroke-mauve-500 hover:stroke-mauve-100" />
                        </button>
                        <x-image-dialog :id="'edit-image-dialog-'.$image->id" :image="$image" :folders="$folders" />

                        <button type="button" class="cursor-pointer" title="Remove" commandfor="remove-image-dialog-{{ $image->id }}" command="show-modal">
                            <x-lucide-trash class="size-4 shrink-0 stroke-mauve-500 hover:stroke-red-400" />
                        </button>
                        <x-confirm-dialog :id="'remove-image-dialog-'.$image->id" :action="route('images.destroy', $image)" method="DELETE">
                            Remove image <span class="font-bold text-mauve-50">{{ $image->name }}</span>?
                        </x-confirm-dialog>
                    </figcaption>
                </figure>
            @endforeach
            <p x-show="!imageFolders.some((folder) => matches(folder))" class="col-span-4 px-2 py-1 text-mauve-500 italic">Nothing here yet</p>
        </div>
    </div>
</div>
