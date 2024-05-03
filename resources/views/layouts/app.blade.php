<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="theme-id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media screen and (min-width: 1024px) {
            .w-e-journal {
                width: calc(100vw - 105px);
            }
        }

        .h-e-journal {
            height: calc(100vh - 280px);
        }
    </style>

    <script type="module">
        if (localStorage.getItem('theme') != null) {
            document.getElementById('theme-id').classList.add(localStorage.getItem('theme'));
        }
        document.getElementById('footer-id').innerHTML = "© 2023 - " + new Date().getFullYear();
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div x-data="setting()" id="app" class="h-screen relative flex bg-white dark:bg-slate-800 text-gray-600 dark:text-white font-sans font-light antialiased overflow-hidden">
        <!-- Desktop sideber -->
        <aside :class="{ 'absolute z-20 w-72': isExpandSideBar, 'w-16': !isExpandSideBar }" class="hidden h-full lg:flex lg:flex-shrink-0 lg:flex-col lg:space-y-6 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-400/20 py-5 transition-all">
            <div class="inline-flex items-center px-2" :class="{ 'justify-center': !isExpandSideBar }">
                <img id="logo-pc-id" x-bind:src="logo" alt="" class="w-12">
                <span x-show="isExpandSideBar" class="ml-2 text-lg font-semibold whitespace-nowrap">Личный кабинет</span>

            </div>
            <div class="relative">
                <button class="absolute -top-7 -right-4 bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded-full border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2.5 py-0.5" @click="isExpandSideBar = !isExpandSideBar">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            <!-- Navigation menu -->
            @include('layouts.navigation-menu')
        </aside>
        <!-- Mobile sidebar -->
        <aside class="lg:hidden fixed z-20 w-80 h-full flex flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-400/20 transition-all duration-700 ease-in-out" :class="{ '-translate-x-full opacity-0': !isSideBarOpen, 'translate-x-0 opacity-100': isSideBarOpen }">
            <div class="flex justify-between p-4">
                <div class="inline-flex items-center" :class="{ 'justify-center': !isSideBarOpen }">
                    <img id="logo-mobile-id" x-bind:src="logo" alt="" class="w-12">
                    <span class="ml-2 text-lg font-semibold whitespace-nowrap">Личный кабинет</span>
                </div>
                <button @click="isSideBarOpen = !isSideBarOpen">
                    <i class="fa-solid fa-xmark fa-xl fa-lg"></i>
                </button>
            </div>
            <!-- Navigation menu -->
            @include('layouts.navigation-menu')
        </aside>

        <div class="w-full flex flex-col">
            <header class="shrink-0 h-16 border-b border-slate-200 dark:border-slate-400/20">
                <div class="h-full flex justify-between lg:justify-end px-5">
                    <!-- Mobile sidebar -->
                    <button @click="isSideBarOpen = !isSideBarOpen" class="block lg:hidden">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
                    <div class="flex items-center space-x-6">
                        <button @click="toggleDarkMode()" class="hover:text-blue-500">
                            <template x-if="!isDarkMode">
                                <i class="fa-solid fa-sun fa-lg"></i>
                            </template>
                            <template x-if="isDarkMode">
                                <i class="fa-solid fa-moon fa-lg"></i>
                            </template>
                        </button>
                        <div class="flex">
                            <span class="bg-slate-200 dark:bg-slate-700 rounded-full border-slate-200 dark:border-slate-400/20 px-3.5 py-3 font-semibold">
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
                    <div class="mb-2">
                        <h1 class="text-lg lg:text-xl font-semibold">{{ $header }}</h1>
                    </div>
                    {{ $slot }}
                </div>
            </main>
            <footer class="shrink-0 w-full h-14 flex items-center justify-center border-t border-slate-200 dark:border-slate-400/20">
                <span id="footer-id"></span>
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
                isExpandSideBar: (localStorage.getItem('isExpandSideBar') === 'open'),
                isSideBarOpen: false,
                toggleSideBar() {
                    this.isExpandSideBar = !this.isExpandSideBar;
                    localStorage.setItem('isExpandSideBar', this.isExpandSideBar ? 'open' : 'close');
                },
                toggleDarkMode() {
                    this.isDarkMode = !this.isDarkMode;
                    localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');

                    if (this.isDarkMode) {
                        document.getElementById("logo-pc-id").src = "{{ asset('storage/logo_dark.png') }}";
                        document.getElementById("logo-mobile-id").src = "{{ asset('storage/logo_dark.png') }}";

                        document.getElementById('theme-id').classList.remove('light');
                        document.getElementById('theme-id').classList.add('dark');
                    } else {
                        document.getElementById("logo-pc-id").src = "{{ asset('storage/logo_light.png') }}";
                        document.getElementById("logo-mobile-id").src = "{{ asset('storage/logo_light.png') }}";

                        document.getElementById('theme-id').classList.remove('dark');
                        document.getElementById('theme-id').classList.add('light');
                    }
                }
            }
        }
    </script>
</body>

</html>