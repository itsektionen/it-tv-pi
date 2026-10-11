@props(['folders', 'selected' => null])

@php
    $byId = $folders->keyBy('id');
    $path = function ($folder) use ($byId, &$path) {
        $parent = $byId->get($folder->parent_id);

        return ($parent ? $path($parent) : '') . '/' . $folder->name;
    };
    $paths = $folders->mapWithKeys(fn ($folder) => [$folder->id => $path($folder)])->sort();
@endphp

<x-select name="folder_id" {{ $attributes }}>
    <x-option value="">Uncategorized</x-option>
    @foreach ($paths as $id => $label)
        <x-option value="{{ $id }}" :selected="$id === $selected">{{ $label }}</x-option>
    @endforeach
</x-select>
