<x-layout title="Users">
    @php
        $users = \App\Models\User::orderBy('id')->get();
    @endphp

    <div class="mb-2 flex gap-2 items-center">
        <x-button class="ml-auto" variant="ghost" commandfor="create-dialog" command="show-modal">Create User</x-button>
        <x-user-dialog id="create-dialog" />
    </div>

    <table class="hidden w-full lg:table">
        <thead>
            <tr class="border-b border-mauve-700 text-left text-mauve-50">
                <th class="px-2 font-bold">#</th>
                <th class="px-2 font-bold">Username</th>
                <th class="px-2 font-bold">Permission</th>
                <th class="px-2 font-bold">Created At</th>
                <th class="px-2 font-bold">Updated At</th>
                <th class="px-2 font-bold w-25"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $u)
                <tr>
                    <td class="px-2 text-mauve-200 italic">{{ $u->id }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->name }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->permission->value }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->created_at }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->updated_at }}</td>
                    <td class="flex justify-end">
                        <x-button variant="ghost" commandfor="edit-dialog-{{ $u->id }}"
                            command="show-modal">Edit</x-button>
                        @unless ($u->is(auth()->user()))
                            <x-button variant="danger" commandfor="remove-dialog-{{ $u->id }}"
                                command="show-modal">Remove</x-button>
                        @endunless
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="space-y-2 lg:hidden">
        @foreach ($users as $u)
            <x-table-card>
                <dt>#</dt>
                <dd>{{ $u->id }}</dd>
                <dt>Username</dt>
                <dd class="truncate text-mauve-50">{{ $u->name }}</dd>
                <dt>Permission</dt>
                <dd>{{ $u->permission->value }}</dd>
                <dt>Created At</dt>
                <dd>{{ $u->created_at }}</dd>
                <dt>Updated At</dt>
                <dd>{{ $u->updated_at }}</dd>

                <x-slot:actions>
                    <x-button variant="ghost" commandfor="edit-dialog-{{ $u->id }}"
                        command="show-modal">Edit</x-button>
                    @unless ($u->is(auth()->user()))
                        <x-button variant="danger" commandfor="remove-dialog-{{ $u->id }}"
                            command="show-modal">Remove</x-button>
                    @endunless
                </x-slot:actions>
            </x-table-card>
        @endforeach
    </div>

    @foreach ($users as $u)
        <x-user-dialog :id="'edit-dialog-' . $u->id" :user="$u" />
        @unless ($u->is(auth()->user()))
            <x-confirm-dialog :id="'remove-dialog-' . $u->id" :action="route('users.destroy', $u)" method="DELETE">
                Remove user <span class="font-bold text-mauve-50">{{ $u->name }}</span>?
            </x-confirm-dialog>
        @endunless
    @endforeach
</x-layout>
