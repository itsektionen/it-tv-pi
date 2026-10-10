@props(['id', 'action', 'method' => 'POST'])

<dialog id="{{ $id }}" closedby="any" class="m-auto w-full max-w-sm bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60">
    <form method="POST" action="{{ $action }}" class="space-y-3">
        @csrf
        @method($method)

        <p>{{ $slot }}</p>

        <div class="flex justify-end gap-2">
            <x-button variant="ghost" commandfor="{{ $id }}" command="close">No</x-button>
            <x-button type="submit">Yes</x-button>
        </div>
    </form>
</dialog>
