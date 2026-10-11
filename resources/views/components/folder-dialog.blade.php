@props(['id'])

<dialog id="{{ $id }}" closedby="any" class="m-auto w-full max-w-sm bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <form method="POST" action="{{ route('folders.store') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="parent_id" :value="parent ?? ''">

        <h2 class="text-lg font-bold text-mauve-50">Creating folder in <span class="font-mono" x-text="path(parent)"></span></h2>
        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Name</dt>
            <dd><x-input name="name" required /></dd>
        </dl>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">Cancel</x-button>
            <x-button type="submit">Create</x-button>
        </div>
    </form>
</dialog>
