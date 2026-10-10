@props(['title' => null])

@php
    $tabs = [
        'dashboard' => ['label' => 'dashboard'],
        'client' =>    ['label' => 'client', 'newTab' => true],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scheme-dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? "{$title} - ITTV π" : 'ITTV π' }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="bg-mauve-950 text-sm text-mauve-200">
        <div class="mx-auto max-w-6xl px-4 py-2">
            <header class="flex items-center gap-4 pb-2">
                <a href="{{ route('dashboard') }}" class="flex size-16 items-center justify-center bg-mauve-800 text-4xl font-bold text-mauve-50">π</a>
                <div>
                    <h1 class="text-2xl font-bold text-mauve-50">ITTV π</h1>
                </div>
            </header>

            <nav class="flex items-end border-b-[3px] border-mauve-700">
                @foreach ($tabs as $route => $tab)
                    <a
                        href="{{ route($route) }}"
                        @if ($tab['newTab'] ?? false) target="_blank" @endif
                        @if (request()->routeIs($route)) aria-current="page" @endif
                        @class([
                            'px-2 py-0.5',
                            'bg-mauve-700 text-mauve-50' => request()->routeIs($route),
                            'text-mauve-400 hover:underline' => ! request()->routeIs($route),
                        ])
                    >{{ $tab['label'] }}</a>
                @endforeach

                <div @class(['ml-auto flex items-end', isset($actions) ? $actions->attributes->get('class') : null])>
                    {{ $actions ?? '' }}
                </div>
            </nav>

            <main class="py-4">
                {{ $slot }}
            </main>
        </div>

    </body>
</html>
