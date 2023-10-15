<x-guest-layout>
    <div class="mx-auto flex flex-col space-y-4 max-w-md px-2 md:px-0">
        <!-- Logo -->
        <div class="mt-4 flex flex-col items-center justify-center">
            <a href="{{ route('login') }}">
                <img src="{{ asset('storage/logo.svg') }}" alt="logo">
            </a>
            <p class="text-2xl text-center">{{ __('ИУБиП|Digital') }}</p>
        </div>
        <!-- Auth user form -->
        <div class="flex flex-col w-full bg-white border border-t-4 border-t-blue-500 rounded-md shadow-xl p-6">
            <form action="{{ route('login') }}" method="post">
                @csrf
                <div>
                    <label class="text-sm" for="name-id">Имя пользователя:</label>
                    <input class="w-full block border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" type="text" name="name" id="name-id" value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name')<span class="text-xs md:text-sm text-red-500 font-bold">{{ $message }}</span>@enderror
                </div>
                <div class="mt-4">
                    <label class="text-sm" for="password-id">Пароль:</label>
                    <input class="w-full block border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" type="password" name="password" id="password-id" required autocomplete="current-password">
                    @error('password')<span class="text-xs md:text-sm text-red-500 font-bold">{{ $message }}</span>@enderror
                </div>
                <div class="mt-4">
                    <button class="w-full block bg-blue-500 hover:bg-blue-600 rounded px-4 py-2 text-white" type="submit">Войти</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>