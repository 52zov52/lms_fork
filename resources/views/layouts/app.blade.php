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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">

    <!-- Styles -->
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 text-gray-600 font-sans font-light antialiased relative lg:flex" x-data="{open: false}">

    <!-- Nav -->
    <nav class="absolute lg:relative z-10 lg:z-0 flex-shrink-0 w-80 h-screen bg-white border border-r-gray-300 shadow-xl px-1 py-4 inset-0 transform lg:transform-none lg:opacity-100 duration-200" :class="{'translate-x-0 ease-in opacity-100':open === true, '-translate-x-full ease-out opacity-0':open === false}">
        <!-- Logo -->
        <div class="hidden lg:block">
            <a class="flex flex-col items-center" href="{{ route('login') }}">
                <img class="w-20" src="{{ asset('storage/logo.png') }}" alt="logo">
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

        <div class="mt-1 flex flex-col space-y-2 items-center font-semibold">
            <div class="flex flex-col items-center font-semibold">
                <span>{{ auth()->user()->userData->last_name }}</span>
                <span>{{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}</span>
            </div>
        </div>

        <div style="height: calc(100vh - 160px);" class="overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
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
                <li class="rounded {{ (request()->routeIs('elibrary')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('elibrary') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-book"></i>
                        <span>Электронные библиотечные системы</span>
                    </a>
                </li>
                <li class="rounded {{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('schedule') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-calendar-days"></i>
                        <span>Расписание</span>
                    </a>
                </li>
                @can('show-operating-activity-and-decisions-rectorate-and-council')
                <li class="rounded {{ (request()->routeIs('operating.activity')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('operating.activity') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Операционная деятельность</span>
                    </a>
                </li>
                <li class="rounded {{ (request()->routeIs('decisions.rectorate')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('decisions.rectorate') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-pen-nib"></i>
                        <span>Решение ректората</span>
                    </a>
                </li>
                <li class="rounded {{ (request()->routeIs('decisions.council')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('decisions.council') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-feather-pointed"></i>
                        <span>Решение уч. совета</span>
                    </a>
                </li>
                @endcan
                @can('access-to-e-journal')
                <li class="rounded {{ (request()->routeIs('my.groups')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('my.groups') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-user-group"></i>
                        <span>Мои группы</span>
                    </a>
                </li>
                @endcan
                @role('Student')
                <li class="rounded {{ (request()->routeIs('document.order.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('document.order.requests') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Заказ документов (справок)</span>
                    </a>
                </li>
                @endrole
                @canany(['access-to-student-requests-for-certificates-full', 'access-to-student-requests-for-certificates-spo', 'access-to-student-requests-for-certificates-vo'])
                <li class="rounded {{ (request()->routeIs('document.retrieval.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('document.retrieval.requests') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-file-import"></i>
                        <span class="flex">Заявки на документы (справки) @livewire('components.application-counter')</span>
                    </a>
                </li>
                @endcan
                @role('Teacher')
                <li class="rounded {{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('contingent') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-people-group"></i>
                        <span>Контингент</span>
                    </a>
                </li>
                @endrole
                @can('access-to-data-on-students-academic-leaves')
                <li class="rounded {{ (request()->routeIs('academic.leave.calendar')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('academic.leave.calendar') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span>Календарь академ. отпусков</span>
                    </a>
                </li>
                @endcan
                <li class="rounded {{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('reports') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>Отчеты и аналитика</span>
                    </a>
                </li>
                <li class="rounded {{ (request()->routeIs('feedback') || request()->routeIs('look.feedback.messange')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }}">
                    <a href="{{ route('feedback') }}" class="w-full inline-flex items-baseline space-x-2 px-2 py-3">
                        <i class="fa-solid fa-comment"></i>
                        <span>Поддержка</span>
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
        </div>
    </nav>

    <div class="relative z-0 h-screen lg:flex-grow overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <header class="z-20 fixed w-full h-14 bg-white shadow-xl px-6">
            <div class="flex items-center justify-between h-full block lg:hidden">
                <a class="flex items-center space-x-2" href="{{ route('login') }}">
                    <img class="w-20" src="{{ asset('storage/logo.png') }}" alt="logo">
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

            <x-alert type="warning" title="Внимание!" message="Ежедневно в период с 00:00 по 03:30 проходит синхронизация информации. В данный промежуток времени платформа может быть недоступна или функции платформы могут работать некорректно" />

            <div class="mt-2">
                {{ $breadcrumb }}
            </div>

            <div class="mb-4">
                <h1 class="text-xl xl:text-2xl font-semibold">{{ $header }}</h1>
            </div>

            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
    @stack('scripts')
</body>

</html>