@props(['id', 'user' => null])

<dialog id="{{ $id }}" closedby="any" class="m-auto w-full max-w-md bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <div class="space-y-3" x-data="{
        name: @js($user?->name ?? ''),
        password: '',
        permission: @js($user?->permission->value ?? \App\Enums\Permission::Viewer->value),
        error: null,
        async save() {
            this.error = null;

            try {
                const response = await fetch(@js($user ? route('users.update', $user) : route('users.store')), {
                    method: @js($user ? 'PUT' : 'POST'),
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) },
                    body: JSON.stringify({ name: this.name, password: this.password || null, permission: this.permission }),
                });

                if (response.ok) {
                    window.location.reload();
                    return;
                }

                this.error = (await response.json()).message;
            } catch {
                this.error = 'Could not save the user.';
            }
        },
    }">
        <h2 class="text-lg font-bold text-mauve-50">{{ $user ? 'Edit user' : 'Create a new user' }}</h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            {{-- autocomplete=off and data-bwignore here to make bitwarden shut the **** up --}}
            {{-- update: it still doesn't shut the **** up because they make bitwarden not respect data-bwignore in newer versions --}}
            {{-- why --}}
            <dt class="text-mauve-400">Username</dt>
            <dd><x-input x-model="name" autocomplete="off" data-bwignore /></dd>
            <dt class="text-mauve-400">Password</dt>
            <dd><x-input x-model="password" type="password" autocomplete="new-password" data-bwignore :placeholder="$user ? 'Leave blank to keep current' : null" /></dd>
            <dt class="text-mauve-400">Permission</dt>
            <dd>
                <x-select x-model="permission">
                    @foreach (\App\Enums\Permission::cases() as $permission)
                        <x-option value="{{ $permission->value }}">{{ ucfirst($permission->value) }}</x-option>
                    @endforeach
                </x-select>
            </dd>
        </dl>

        <p x-show="error" x-text="error" class="border border-red-800 bg-red-950 px-2 py-1 text-red-300"></p>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button x-on:click="save()">{{ $user ? 'Save' : 'Create' }}</x-button>
        </div>
    </div>
</dialog>
