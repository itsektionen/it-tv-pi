<x-layout title="login">
    <form method="POST" action="{{ route('login') }}" class="max-w-sm space-y-3">
        @csrf

        <dl class="grid grid-cols-[100px_1fr] items-center gap-y-1">
            <dt class="text-mauve-400">Username</dt>
            <dd><x-input name="name" :value="old('name')" autocomplete="username" required autofocus /></dd>
            <dt class="text-mauve-400">Password</dt>
            <dd><x-input name="password" type="password" autocomplete="current-password" required /></dd>
        </dl>

        @error('name')
            <p class="border border-red-800 bg-red-950 px-2 py-1 text-red-300">{{ $message }}</p>
        @enderror

        <div class="flex justify-end">
            <x-button type="submit">Login</x-button>
        </div>
    </form>
</x-layout>
