@props(['slideshows', 'selected' => null])

<x-select name="slideshow_id" {{ $attributes }}>
    <x-option value="">None</x-option>
    @foreach ($slideshows as $slideshow)
        <x-option value="{{ $slideshow->id }}" :selected="$slideshow->id === $selected">{{ $slideshow->name }}</x-option>
    @endforeach
</x-select>
