<x-layout title="API Keys">
    @php
        $apiKeys = \App\Models\ApiKey::orderBy('id')->get();
    @endphp

    <div class="mb-2 flex gap-2 items-center">
        <x-button class="ml-auto" variant="ghost" commandfor="create-dialog" command="show-modal">Create API Key</x-button>
        <x-api-key-dialog id="create-dialog" />
    </div>

    <table class="hidden w-full lg:table">
        <thead>
            <tr class="border-b border-mauve-700 text-left text-mauve-50">
                <th class="px-2 font-bold">#</th>
                <th class="px-2 font-bold">Name</th>
                <th class="px-2 font-bold">Permission</th>
                <th class="px-2 font-bold">Last Used</th>
                <th class="px-2 font-bold">Times Used</th>
                <th class="px-2 font-bold">Created At</th>
                <th class="px-2 font-bold w-25"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($apiKeys as $k)
                <tr>
                    <td class="px-2 text-mauve-200 italic">{{ $k->id }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $k->name }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $k->permission->value }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $k->last_used ?? 'Never' }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $k->times_used }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $k->created_at }}</td>
                    <td class="flex justify-end">
                        <x-button variant="danger" commandfor="revoke-dialog-{{ $k->id }}"
                            command="show-modal">Revoke</x-button>
                    </td>
                </tr>
            @endforeach
            @if ($apiKeys->isEmpty())
                <tr>
                    <td colspan="7" class="px-2 text-mauve-500">No API keys</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="space-y-2 lg:hidden">
        @foreach ($apiKeys as $k)
            <x-table-card>
                <dt>#</dt>
                <dd>{{ $k->id }}</dd>
                <dt>Name</dt>
                <dd class="truncate text-mauve-50">{{ $k->name }}</dd>
                <dt>Permission</dt>
                <dd>{{ $k->permission->value }}</dd>
                <dt>Last Used</dt>
                <dd>{{ $k->last_used ?? 'Never' }}</dd>
                <dt>Times Used</dt>
                <dd>{{ $k->times_used }}</dd>
                <dt>Created At</dt>
                <dd>{{ $k->created_at }}</dd>

                <x-slot:actions>
                    <x-button variant="danger" commandfor="revoke-dialog-{{ $k->id }}"
                        command="show-modal">Revoke</x-button>
                </x-slot:actions>
            </x-table-card>
        @endforeach
        @if ($apiKeys->isEmpty())
            <p class="text-mauve-500">No API keys</p>
        @endif
    </div>

    @foreach ($apiKeys as $k)
        <x-confirm-dialog :id="'revoke-dialog-' . $k->id" :action="route('api-keys.destroy', $k)" method="DELETE">
            Revoke API key <span class="font-bold text-mauve-50">{{ $k->name }}</span>?
        </x-confirm-dialog>
    @endforeach
</x-layout>
