<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 text-gray-600 font-sans font-light antialiased relative lg:flex" x-data="{open: false}">

    <!-- Nav -->
    <nav class="absolute lg:relative z-10 lg:z-0 flex-shrink-0 w-72 h-screen bg-white border border-r-gray-300 shadow-xl px-2 py-4 inset-0 transform lg:transform-none lg:opacity-100 duration-200" :class="{'translate-x-0 ease-in opacity-100':open === true, '-translate-x-full ease-out opacity-0':open === false}">
        <!-- Logo -->
        <div class="hidden lg:block">
            <a class="flex flex-col items-center" href="{{ route('login') }}">
                <img class="w-20" src="{{ asset('storage/logo.svg') }}" alt="logo">
                <p class="text-xl">{{ __('ИУБиП|Digital') }}</p>
            </a>
        </div>
        <!-- Mobile nav -->
        <div class="flex justify-between block lg:hidden">
            <span class="text-2xl font-semibold">Навигация</span>
            <button @click="open = false" class="rounded-full px-3 py-1 hover:bg-blue-500 hover:text-white">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        </div>

        <div class="mt-2 flex flex-col items-center font-semibold">
            <span>{{ auth()->user()->userData->last_name }}</span>
            <span>{{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}</span>
        </div>
        
        <ul class="mt-2 flex flex-col space-y-1">
            <li class="rounded {{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-house"></i>
                    <span>Главная</span>
                </a>
            </li>
            <li class="rounded hover:bg-blue-500 hover:text-white">
                <a href="https://eiosiubip.ru/login/index.php" target="_blank" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>Система онлайн-обучения</span>
                </a>
            </li>
            <li class="rounded {{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                <a href="{{ route('schedule') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Расписание</span>
                </a>
            </li>
            <li class="rounded {{ (request()->routeIs('project.board')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                <a href="{{ route('project.board') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Управление проектами</span>
                </a>
            </li>
            <li class="rounded {{ (request()->routeIs('document.order.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                <a href="{{ route('document.order.requests') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Заказ документов</span>
                </a>
            </li>
            <li class="rounded {{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                <a href="{{ route('contingent') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Контингент</span>
                </a>
            </li>
            <li class="rounded {{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                <a href="{{ route('reports') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Отчеты и аналитика</span>
                </a>
            </li>
            <li class="rounded hover:bg-blue-500 hover:text-white">
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <a @click.prevent="$root.submit();" href="{{ route('logout') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Выход</span>
                    </a>
                </form>
            </li>
        </ul>
    </nav>

    <div class="relative z-0 h-screen lg:flex-grow overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <header class="fixed w-full h-14 bg-white shadow-xl px-6">
            <div class="flex items-center justify-between h-full block lg:hidden">
                <a class="flex items-center space-x-2" href="{{ route('login') }}">
                    <img class="w-16" src="{{ asset('storage/logo.svg') }}" alt="logo">
                    <p class="text-base">{{ __('ИУБиП|Digital') }}</p>
                </a>
                <template x-if="!open">
                    <button @click="open = true" class="rounded p-2 hover:bg-blue-500 hover:text-white">
                        <i class="fa-solid fa-bars fa-xl"></i>
                    </button>
                </template>
                <template x-if="open">
                    <button @click="open = false" class="rounded p-2 hover:bg-blue-500 hover:text-white">
                        <i class="fa-solid fa-xmark fa-xl"></i>
                    </button>
                </template>
            </div>
        </header>
        <!-- Content -->
        <main class="mt-16 px-6 py-4">
            <div class="mb-4">
                <h1 class="text-xl xl:text-2xl font-semibold">{{ $header }}</h1>
            </div>

            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
</body>

</html>