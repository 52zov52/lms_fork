<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="theme">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Личный кабинет ИУБиП') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media screen and (min-width: 1024px) {
            .w-admission-campaign {
                width: calc(100vw - 105px);
            }

            .w-e-journal {
                width: calc(100vw - 105px);
            }
        }

        .h-e-journal {
            height: calc(100vh - 300px);
        }
    </style>

    <script type="module">
        if (localStorage.getItem('theme') != null) {
            document.getElementById('theme').classList.add(localStorage.getItem('theme'));
        }
        document.getElementById('footer').innerHTML = "ЧОУ ВО ЮУ (ИУБиП) © 2023 - " + new Date().getFullYear();
    </script>

    <!-- Styles -->
    @livewireStyles

    <script src="{{ asset('public/js/moment.min.js') }}"></script>
    <script src="{{ asset('public/js/pikaday.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/css/pikaday.css') }}">
</head>

<body>
    <div x-data="setting()" id="app" class="h-screen relative flex bg-white dark:bg-slate-800 text-gray-600 dark:text-white font-sans font-light antialiased overflow-hidden">
        <aside x-cloak :class="{ 'w-16': !isSidebarExpanded, 'w-72': isSidebarExpanded }" class="hidden z-30 lg:flex lg:flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-400/20 py-5">
            <div :class="{ 'justify-center': !isSidebarExpanded && !isMobileMenuVisible, '': isSidebarExpanded || isMobileMenuVisible }" class="inline-flex items-center px-2">
                <img id="logo" x-bind:src="logo" class="w-12 h-8">
                <span x-show="isSidebarExpanded || isMobileMenuVisible" class="ml-2 text-lg font-semibold whitespace-nowrap">
                    Личный кабинет
                </span>
            </div>
            <div class="relative">
                <button @click="isSidebarExpanded = !isSidebarExpanded" class="absolute -right-3 bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded-full border-slate-200 hover:border-blue-400 dark:border-slate-400/20 px-2 hover:text-white dark:text-white">
                    <template x-if="!isSidebarExpanded">
                        <i class="fa-solid fa-chevron-right fa-xs"></i>
                    </template>
                    <template x-if="isSidebarExpanded">
                        <i class="fa-solid fa-chevron-left fa-xs"></i>
                    </template>
                </button>
            </div>
            @include('layouts.menu.nav-menu')
        </aside>

        <div class="w-full flex flex-col">
            <header class="shrink-0 h-16 border-b border-slate-200 dark:border-slate-400/20">
                <div class="h-full flex justify-between lg:justify-end px-5">
                    <!-- Mobile sidebar -->
                    <button @click="isSideBarOpen = !isSideBarOpen" class="block lg:hidden">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
                    <div class="flex items-center space-x-6">
                        <button @click="toggleDarkMode()" class="hover:text-blue-400">
                            <template x-if="!isDarkMode">
                                <i class="fa-solid fa-sun fa-lg"></i>
                            </template>
                            <template x-if="isDarkMode">
                                <i class="fa-solid fa-moon fa-lg"></i>
                            </template>
                        </button>
                        <div class="flex">
                            <span class="bg-slate-200 dark:bg-slate-700 rounded-full border-slate-200 dark:border-slate-400/20 px-3 py-3 font-semibold">
                                {{ mb_substr(auth()->user()->userData->last_name, 0, 1) }}{{ mb_substr(auth()->user()->userData->first_name, 0, 1) }}
                            </span>
                            <div class="ml-2">
                                <p>{{ auth()->user()->userData->last_name }}</p>
                                <p>{{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="grow p-5 overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
                <div class="flex flex-col">
                    <div class="mb-4">
                        <h1 class="text-lg lg:text-2xl font-semibold">{{ $header }}</h1>
                    </div>
                    {{ $slot }}
                </div>
            </main>
            <footer class="shrink-0 w-full h-14 flex items-center justify-center border-t border-slate-200 dark:border-slate-400/20">
                <span id="footer"></span>
            </footer>
        </div>
    </div>

    @stack('modals')

    @livewireScripts
    @stack('scripts')

    <script>
        function setting() {
            return {
                logo: (localStorage.getItem('theme') === 'dark') ? "{{ asset('storage/logo_dark.png') }}" : "{{ asset('storage/logo_light.png') }}",
                isDarkMode: (localStorage.getItem('theme') === 'dark'),
                isSidebarExpanded: false,
                isMobileMenuVisible: false,
                isTooltipVisible: false,
                toggleSideBar() {
                    this.isSidebarExpanded = !this.isSidebarExpanded;
                },
                toggleMobileMenu() {
                    this.isMobileMenuVisible = !this.isMobileMenuVisible;
                },
                toggleDarkMode() {
                    this.isDarkMode = !this.isDarkMode;
                    localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');

                    if (this.isDarkMode) {
                        document.getElementById("logo").src = "{{ asset('storage/logo_dark.png') }}";

                        document.getElementById('theme').classList.remove('light');
                        document.getElementById('theme').classList.add('dark');
                    } else {
                        document.getElementById("logo").src = "{{ asset('storage/logo_light.png') }}";

                        document.getElementById('theme').classList.remove('dark');
                        document.getElementById('theme').classList.add('light');
                    }
                }
            }
        }
    </script>
</body>

</html>