@props(['id'])

<dialog id="{{ $id }}" :closedby="token ? 'none' : 'any'"
    class="m-auto w-full max-w-md bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60" x-data="{
        name: '',
        permission: @js(\App\Enums\Permission::Manager->value),
        token: null,
        error: null,
        saving: false,
        async save() {
            this.error = null;
            this.saving = true;

            try {
                const { ok, data } = await sendJson(@js(route('api-keys.store')), {
                    body: { name: this.name, permission: this.permission },
                });
                if (ok) {
                    this.token = data.token;
                    return;
                }
                this.error = data.message;
            } catch {
                this.error = 'Could not create the API key.';
            }

            this.saving = false;
        },
    }" x-on:close="token && window.location.reload()">
    <div class="space-y-3" x-show="!token">
        <h2 class="text-lg font-bold text-mauve-50">Create a new API key</h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Name</dt>
            <dd><x-input x-model="name" autocomplete="off" /></dd>
            <dt class="text-mauve-400">Permission</dt>
            <dd>
                <x-select x-model="permission">
                    @foreach ([\App\Enums\Permission::Manager, \App\Enums\Permission::Admin] as $permission)
                        <x-option value="{{ $permission->value }}">{{ ucfirst($permission->value) }}</x-option>
                    @endforeach
                </x-select>
            </dd>
        </dl>

        <p x-show="error" x-text="error" class="border border-red-800 bg-red-950 px-2 py-1 text-red-300"></p>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button x-on:click="save()" ::disabled="saving"
                class="disabled:cursor-default disabled:opacity-50">Create</x-button>
        </div>
    </div>

    <div class="space-y-3" x-show="token">
        <h2 class="text-lg font-bold text-mauve-50">API key created</h2>
        <p>Copy the key now, it won't be shown again.</p>
        <div class="flex gap-2">
            <x-input ::value="token" readonly x-on:focus="$el.select()" class="font-mono" />
            <x-button variant="outline" x-on:click="navigator.clipboard.writeText(token)">Copy</x-button>
        </div>

        <div class="flex justify-end">
            <x-button commandfor="{{ $id }}" command="close">Done</x-button>
        </div>
    </div>
</dialog>
