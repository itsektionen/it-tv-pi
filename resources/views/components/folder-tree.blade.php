@props(['folders', 'parent' => null])

<ul @class(['ml-2 border-l border-mauve-700 pl-1' => $parent])>
    @foreach ($folders->get($parent ?? '', []) as $folder)
        <li>
            <div class="flex items-center"
                :class="selected === @js($folder->id) ? 'bg-mauve-700 text-mauve-50' : 'text-mauve-400'">
                <button type="button" class="min-w-0 flex-1 truncate px-2 py-0.5 text-left"
                    :class="selected !== @js($folder->id) && 'hover:underline'"
                    x-on:click="selected = @js($folder->id)">{{ $folder->name }}</button>
                @can('manager')
                    <button type="button" class="cursor-pointer px-1" title="Create folder in {{ $folder->name }}"
                        x-show="selected === @js($folder->id)" commandfor="create-folder-dialog"
                        command="show-modal" x-on:click="parent = @js($folder->id)"><x-lucide-folder-plus
                            class="size-4 stroke-mauve-300 hover:stroke-mauve-50" /></button>
                    <button type="button" class="cursor-pointer px-1" title="Remove {{ $folder->name }}"
                        x-show="selected === @js($folder->id)"
                        commandfor="remove-folder-dialog-{{ $folder->id }}" command="show-modal"><x-lucide-trash
                            class="size-4 stroke-mauve-300 hover:stroke-red-400" /></button>
                @endcan
            </div>
            @can('manager')
                <x-confirm-dialog :id="'remove-folder-dialog-' . $folder->id" :action="route('folders.destroy', $folder)" method="DELETE">
                    Remove folder <span class="font-bold text-mauve-50">{{ $folder->name }}</span>? Its subfolders move up
                    one level and its images become uncategorized.
                </x-confirm-dialog>
            @endcan
            @if ($folders->has($folder->id))
                <x-folder-tree :folders="$folders" :parent="$folder->id" />
            @endif
        </li>
    @endforeach
</ul>
