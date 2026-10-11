<x-layout title="Screens">
    @php
        $screens = \App\Models\Screen::all();
        $screenGroups = \App\Models\ScreenGroup::with("screens")->get();
        $slideshows = \App\Models\Slideshow::orderBy("name")->get(["id", "name"]);
    @endphp

    <h2 class="mb-2 text-lg font-bold text-mauve-50">Screens</h2>
    <table class="w-full">
        <thead>
            <tr class="border-b border-mauve-700 text-left text-mauve-50">
                <th class="px-2 font-bold">Id</th>
                <th class="px-2 font-bold">Name</th>
                <th class="px-2 font-bold">Status</th>
                <th class="px-2 font-bold">Slideshow</th>
                <th class="px-2 font-bold">Last Active</th>
                <th class="px-2 font-bold">Created At</th>
                <th class="px-2 font-bold w-25"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($screens as $s)
                <tr>
                    <td class="px-2 text-mauve-200 italic">{{ $s->id }}</td>
                    <td @class(['px-2 italic', 'text-mauve-50' => $s->name,   'text-mauve-500' => !$s->name])  >{{ $s->name ?? 'No name set' }}</td>
                    <td @class(['px-2 italic', 'text-mauve-50' => $s->active, 'text-mauve-500' => !$s->active])>{{ $s->active ? 'Active' : 'Inactive' }}</td>
                    <td class="px-2">
                        <form method="POST" action="{{ route('screens.activate', $s) }}">
                            @csrf
                            <x-slideshow-select :slideshows="$slideshows" :selected="$s->slideshow_id" onchange="this.form.requestSubmit()" />
                        </form>
                    </td>
                    <td class="px-2 text-mauve-200 italic">{{ $s->last_active_at ?? 'Never' }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $s->created_at }}</td>
                    <td class="flex justify-end">
                        <x-button variant="ghost" popovertarget="preview-{{ $s->id }}" style="anchor-name: --preview-{{ $s->id }}">Preview</x-button>
                        <div
                            id="preview-{{ $s->id }}"
                            popover
                            class="inset-auto m-0 mt-1 border border-mauve-700 bg-black p-0"
                            style="position-anchor: --preview-{{ $s->id }}; position-area: bottom span-left;"
                        >
                            <iframe src="{{ route('client', ["id" => $s]) }}" loading="lazy" class="block aspect-video w-120"></iframe>
                        </div>
                        <x-button variant="ghost" commandfor="edit-dialog-{{ $s->id }}" command="show-modal">Edit</x-button>
                        <x-screen-dialog :id="'edit-dialog-'.$s->id" :screen="$s" />
                        <x-button variant="danger" commandfor="remove-dialog-{{ $s->id }}" command="show-modal">Remove</x-button>
                        <x-confirm-dialog :id="'remove-dialog-'.$s->id" :action="route('screens.destroy', $s)" method="DELETE">
                            Remove screen <span class="font-bold text-mauve-50">{{ $s->name ?? $s->id }}</span>?
                        </x-confirm-dialog>
                    </td>
                </tr>
            @endforeach
            @if ($screens->isEmpty())
                <tr>
                    <td colspan="7" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="mt-8 mb-2 flex items-center">
        <h2 class="text-lg font-bold text-mauve-50">Screen Groups</h2>
        <x-button variant="ghost" class="ml-auto" commandfor="create-group-dialog" command="show-modal">Create Group</x-button>
        <x-screen-group-dialog id="create-group-dialog" :screens="$screens" />
    </div>
    <table class="w-full">
        <thead>
            <tr class="border-b border-mauve-700 text-left text-mauve-50">
                <th class="px-2 font-bold">#</th>
                <th class="px-2 font-bold">Name</th>
                <th class="px-2 font-bold">Screens</th>
                <th class="px-2 font-bold w-25"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($screenGroups as $g)
                <tr>
                    <td class="px-2 text-mauve-200 italic">{{ $g->id }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $g->name }}</td>
                    <td class="px-2">
                        <div class="flex flex-wrap gap-x-1">
                            @forelse ($g->screens as $screen)
                                <span class="flex"><x-screen-label :screen="$screen" />@if (! $loop->last)<span>,</span>@endif</span>
                            @empty
                                <span class="text-mauve-500 italic">No screens</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="flex justify-end">
                        <x-button variant="ghost" class="whitespace-nowrap" commandfor="slideshow-group-dialog-{{ $g->id }}" command="show-modal">Set Slideshow</x-button>
                        <x-screen-group-slideshow-dialog :id="'slideshow-group-dialog-'.$g->id" :screen-group="$g" :slideshows="$slideshows" />
                        <x-button variant="ghost" commandfor="edit-group-dialog-{{ $g->id }}" command="show-modal">Edit</x-button>
                        <x-screen-group-dialog :id="'edit-group-dialog-'.$g->id" :screens="$screens" :screen-group="$g" />
                        <x-button variant="danger" commandfor="remove-group-dialog-{{ $g->id }}" command="show-modal">Remove</x-button>
                        <x-confirm-dialog :id="'remove-group-dialog-'.$g->id" :action="route('screen-groups.destroy', $g)" method="DELETE">
                            Remove screen group <span class="font-bold text-mauve-50">{{ $g->name }}</span>?
                        </x-confirm-dialog>
                    </td>
                </tr>
            @endforeach
            @if ($screenGroups->isEmpty())
                <tr>
                    <td colspan="4" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                </tr>
            @endif
        </tbody>
    </table>
</x-layout>
