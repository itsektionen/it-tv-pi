@props(['variant' => 'default'])

<button {{ $attributes->merge(['type' => 'button'])->class([
    'cursor-pointer border px-2 py-0.5',
    match ($variant) {
        'ghost' => 'border-transparent text-blue-400 hover:underline',
        'muted' => 'border-transparent text-mauve-400 hover:underline',
        'danger' => 'border-transparent text-red-400 hover:underline',
        'outline' => 'border-mauve-700 text-mauve-200 hover:bg-mauve-800',
        default => 'border-mauve-700 bg-mauve-700 text-mauve-50 hover:border-mauve-600 hover:bg-mauve-600',
    },
]) }}>{{ $slot }}</button>
