@props(['id', 'screen'])

<dialog id="{{ $id }}" closedby="any" class="m-auto w-full max-w-sm bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <form method="POST" action="{{ route('screens.update', $screen) }}" class="space-y-3">
        @csrf
        @method('PUT')

        <h2 class="text-lg font-bold text-mauve-50">Edit screen</h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Name</dt>
            <dd><x-input name="name" value="{{ $screen->name }}" /></dd>
        </dl>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button type="submit">Save</x-button>
        </div>
    </form>
</dialog>
