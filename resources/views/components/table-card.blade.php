<div {{ $attributes->class('space-y-3 bg-mauve-900 p-4 text-left text-sm text-mauve-200') }}>
    <div class="flex gap-3">
        <dl class="grid min-w-0 flex-1 grid-cols-[100px_minmax(0,1fr)] items-center gap-y-1 [&>dt]:text-mauve-400">
            {{ $slot }}
        </dl>
        @isset($actions)
            <div class="flex flex-col items-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
    {{ $footer ?? '' }}
</div>
