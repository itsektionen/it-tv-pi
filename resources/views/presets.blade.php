<x-layout>
    @php
        $presets = \App\Models\Preset::orderByDesc('id')->get();
        $screenGroups = \App\Models\ScreenGroup::all();
        $slideshows = \App\Models\Slideshow::all();
    @endphp

    <div x-data="{}">
        <div class="mb-2 flex gap-2 items-center">
            @can('manager')
                <x-button class="ml-auto" variant="ghost" commandfor="create-dialog" command="show-modal">Create
                    Preset</x-button>
                <x-preset-dialog id="create-dialog" :screenGroups="$screenGroups" :slideshows="$slideshows" />
            @endcan
        </div>

        <table class="hidden w-full lg:table">
            <thead>
                <tr class="border-b border-mauve-700 text-left text-mauve-50">
                    <th class="px-2 font-bold">#</th>
                    <th class="px-2 font-bold">Name</th>
                    <th class="px-2 font-bold">Last Activated</th>
                    <th class="px-2 font-bold">Created At</th>
                    <th class="px-2 font-bold w-25"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presets as $p)
                    <tr>
                        <td class="px-2 text-mauve-200 italic">{{ $p->id }}</td>
                        <td class="px-2 text-mauve-200 italic">{{ $p->name }}</td>
                        <td @class([
                            'px-2 italic',
                            'text-mauve-50' => $p->last_activated,
                            'text-mauve-500' => !$p->last_activated,
                        ])>
                            {{ $p->last_activated ? $p->last_activated : 'Never' }}</td>

                        <td class="px-2 text-mauve-200 italic">{{ $p->created_at }}</td>
                        <td class="flex justify-end">
                            @can('manager')
                                <x-button variant="ghost" commandfor="activate-dialog-{{ $p->id }}"
                                    command="show-modal">Activate</x-button>
                            @endcan
                            @can('admin')
                                <x-button variant="ghost" commandfor="edit-dialog-{{ $p->id }}"
                                    command="show-modal">Edit</x-button>
                                <x-button variant="danger" commandfor="remove-dialog-{{ $p->id }}"
                                    command="show-modal">Remove</x-button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                @if ($presets->isEmpty())
                    <tr>
                        <td colspan="5" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="space-y-2 lg:hidden">
            @foreach ($presets as $p)
                <x-table-card>
                    <dt>#</dt>
                    <dd>{{ $p->id }}</dd>
                    <dt>Name</dt>
                    <dd class="truncate text-mauve-50">{{ $p->name }}</dd>
                    <dt>Last Activated</dt>
                    <dd @class([
                        'text-mauve-50' => $p->last_activated,
                        'text-mauve-500' => !$p->last_activated,
                    ])>
                        {{ $p->last_activated ? $p->last_activated : 'Never' }}</dd>
                    <dt>Created At</dt>
                    <dd>{{ $p->created_at }}</dd>

                    <x-slot:actions>
                        @can('manager')
                            <x-button variant="ghost" commandfor="activate-dialog-{{ $p->id }}"
                                command="show-modal">Activate</x-button>
                        @endcan
                        @can('admin')
                            <x-button variant="ghost" commandfor="edit-dialog-{{ $p->id }}"
                                command="show-modal">Edit</x-button>
                            <x-button variant="danger" commandfor="remove-dialog-{{ $p->id }}"
                                command="show-modal">Remove</x-button>
                        @endcan
                    </x-slot:actions>
                </x-table-card>
            @endforeach
            @if ($presets->isEmpty())
                <p class="px-2 py-1 text-mauve-500 italic">Nothing here yet</p>
            @endif
        </div>

        @foreach ($presets as $p)
            @can('manager')
                <x-confirm-dialog :id="'activate-dialog-' . $p->id" :action="route('presets.activate', $p)">
                    Activate preset <span class="font-bold text-mauve-50">{{ $p->name }}</span>?
                </x-confirm-dialog>
            @endcan
            @can('admin')
                <x-preset-dialog :id="'edit-dialog-' . $p->id" :preset="$p" :screenGroups="$screenGroups" :slideshows="$slideshows" />
                <x-confirm-dialog :id="'remove-dialog-' . $p->id" :action="route('presets.destroy', $p)" method="DELETE">
                    Remove preset <span class="font-bold text-mauve-50">{{ $p->name }}</span>?
                </x-confirm-dialog>
            @endcan
        @endforeach
    </div>
</x-layout>
