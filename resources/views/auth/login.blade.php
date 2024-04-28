<x-guest-layout>
    <div class="mx-auto flex flex-col space-y-4 max-w-md px-2 md:px-0">
        <!-- Logo -->
        <div class="mt-4 flex flex-col items-center justify-center">
            <a href="{{ route('login') }}">
                <img id="logo-pc-id" x-bind:src="logo" alt="" class="w-24">
            </a>
            <p class="text-2xl text-center font-semibold">{{ __('ИУБиП|Digital') }}</p>
        </div>
        <!-- Auth user form -->
        <div class="flex flex-col w-full bg-white dark:bg-slate-700 border rounded border-slate-400/20 p-6">
            @error('name')
            <div class="flex items-center bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
                <i class="fa-solid fa-xmark fa-lg"></i>
                <div class="ml-3 text-base font-medium">
                    {{ $message }}
                </div>
            </div>
            @enderror
            <form action="{{ route('login') }}" method="post">
                @csrf
                <div>
                    <label class="text-sm font-semibold" for="name-id">Имя пользователя:</label>
                    <input class="w-full block bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" type="text" name="name" id="name-id" value="{{ old('name') }}" required autofocus autocomplete="name">
                </div>
                <div class="mt-4">
                    <label class="text-sm font-semibold" for="password-id">Пароль:</label>
                    <input class="w-full block bbg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" type="password" name="password" id="password-id" required autocomplete="current-password">
                </div>
                <div class="mt-4">
                    <button class="w-full block bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-4 py-2 text-blue-400 font-semibold" type="submit">Войти</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>