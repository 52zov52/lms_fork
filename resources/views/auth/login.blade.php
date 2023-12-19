<x-guest-layout>
    <div class="mx-auto flex flex-col space-y-4 max-w-md px-2 md:px-0">
        <!-- Logo -->
        <div class="mt-4 flex flex-col items-center justify-center">
            <a href="{{ route('login') }}">
                <img src="{{ asset('storage/logo.png') }}" alt="logo">
            </a>
            <p class="text-2xl text-center font-semibold">{{ __('ИУБиП|Digital') }}</p>
        </div>
        <!-- Auth user form -->
        <div class="flex flex-col w-full bg-white border border-t-4 border-t-blue-500 rounded-md shadow-xl p-6">
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
                    <input class="w-full block border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" type="text" name="name" id="name-id" value="{{ old('name') }}" required autofocus autocomplete="name">
                </div>
                <div class="mt-4">
                    <label class="text-sm font-semibold" for="password-id">Пароль:</label>
                    <input class="w-full block border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" type="password" name="password" id="password-id" required autocomplete="current-password">
                </div>
                <div class="mt-4">
                    <button class="w-full block bg-blue-500 hover:bg-blue-600 rounded px-4 py-2 text-white font-semibold" type="submit">Войти</button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>