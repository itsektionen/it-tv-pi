@props(['id'])

<dialog
    id="{{ $id }}"
    closedby="any"
    class="mx-auto mt-auto w-full max-w-dvw md:max-w-[calc(100dvw-4rem)] h-[calc(100dvh-2rem)] bg-mauve-900 p-4 text-left text-sm text-mauve-200 backdrop:bg-black/60"
    x-data="{
        select: null,
        open(select) {
            this.select = select;
            document.getElementById(@js($id)).showModal();
        },
        pick(image) {
            this.select(image);
            document.getElementById(@js($id)).close();
        },
    }"
    x-on:pick-image.window="open($event.detail)"
>
    <div class="space-y-3">
        <div class="flex justify-between">
            <h2 class="text-lg font-bold text-mauve-50">Select an image</h2>
            <x-button variant="ghost" commandfor="{{ $id }}" command="close" class="text-mauve-500">Close</x-button>
        </div>

        <x-image-library on-image-click="pick(image)" />
    </div>
</dialog>
