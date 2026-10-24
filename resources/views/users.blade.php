<x-layout title="Users">
    <div class="mb-2 flex gap-2 items-center">
        <x-button class="ml-auto" variant="ghost" commandfor="create-dialog" command="show-modal">Create User</x-button>
        <x-user-dialog id="create-dialog" />
    </div>

    <table class="w-full">
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
            @foreach (\App\Models\User::orderBy('id')->get() as $u)
                <tr>
                    <td class="px-2 text-mauve-200 italic">{{ $u->id }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->name }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->permission->value }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->created_at }}</td>
                    <td class="px-2 text-mauve-200 italic">{{ $u->updated_at }}</td>
                    <td class="flex justify-end">
                        <x-button variant="ghost" commandfor="edit-dialog-{{ $u->id }}"
                            command="show-modal">Edit</x-button>
                        <x-user-dialog :id="'edit-dialog-' . $u->id" :user="$u" />
                        @unless ($u->is(auth()->user()))
                            <x-button variant="danger" commandfor="remove-dialog-{{ $u->id }}"
                                command="show-modal">Remove</x-button>
                            <x-confirm-dialog :id="'remove-dialog-' . $u->id" :action="route('users.destroy', $u)" method="DELETE">
                                Remove user <span class="font-bold text-mauve-50">{{ $u->name }}</span>?
                            </x-confirm-dialog>
                        @endunless
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layout>
