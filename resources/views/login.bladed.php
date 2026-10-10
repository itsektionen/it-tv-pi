<x-layout>
    <table class="w-full">
        <thead>
            <tr class="border-b border-mauve-700 text-left text-mauve-50">
                <th class="px-2 font-bold">#</th>
                <th class="px-2 font-bold">Name</th>
                <th class="px-2 font-bold">Created At</th>
                <th class="px-2 font-bold">Updated At</th>
                <th class="px-2 font-bold w-25"></th>
            </tr>
        </thead>
        <tbody>
            @foreach (\App\Models\Slideshow::with("items")->orderByDesc("id")->get() as $s)
                <tr>
                    <td class="px-2 py-1 text-mauve-200 italic">{{ $s->id }}</td>
                    <td class="px-2 py-1 text-mauve-200 italic">{{ $s->name }}</td>
                    <td class="px-2 py-1 text-mauve-200 italic">{{ $s->created_at }}</td>
                    <td class="px-2 py-1 text-mauve-200 italic">{{ $s->updated_at }}</td>
                    <td class="px-2 py-1 text-blue-400 flex gap-3 justify-end">
                        @if ($s->active)
                            <span class="text-mauve-400">Active</span>
                        @else
                            <form method="POST" action="{{ route('slideshows.activate', $s) }}">
                                <button class="cursor-pointer hover:underline">Activate</button>
                            </form>
                        @endif
                        {{-- TODO: Investigate wether this is bad for hundreds of slideshows eg. having a lot of hidden previews. 
                             Maybe we'd wanna consider having a persistent preview that is moved around but i think html handles this decently.  --}}
                        <button type="button" class="cursor-pointer hover:underline" popovertarget="preview-{{ $s->id }}" style="anchor-name: --preview-{{ $s->id }}">Preview</button>
                        <div
                            id="preview-{{ $s->id }}"
                            popover
                            class="inset-auto m-0 mt-1 border border-mauve-700 bg-black p-0"
                            style="position-anchor: --preview-{{ $s->id }}; position-area: bottom span-left;"
                        >
                            <iframe src="{{ route('preview', $s) }}" loading="lazy" class="block aspect-video w-120"></iframe>
                        </div>
                        {{-- TODO: Investigate the same here --..-- --}}
                        <button type="button" class="cursor-pointer hover:underline" commandfor="edit-dialog-{{ $s->id }}" command="show-modal">Edit</button>
                        <x-slideshow-dialog :id="'edit-dialog-'.$s->id" :slideshow="$s" />
                    </td>
                </tr>
            @endforeach
            @if (\App\Models\Slideshow::all()->isEmpty())
                <tr>
                    <td colspan="5" class="px-2 py-1 text-mauve-500 italic">Nothing here yet</td>
                </tr>
            @endif
        </tbody>
    </table>
</x-layout>