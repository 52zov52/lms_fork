<!DOCTYPE html>
<html lang="en" id="theme-id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    <div id="app" x-data="setting()" class="h-screen overflow-hidden flex bg-white dark:bg-slate-800 text-gray-600 dark:text-white font-sans font-light antialiased">
        <!-- Desktop sideber -->
        <aside class="hidden h-full lg:flex lg:flex-col lg:space-y-6 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-400/20 py-5 transition-all duration-700 ease-in-out"
            :class="{ 'w-16': !isExpandSideBar, 'w-72': isExpandSideBar }">
            <div class="inline-flex items-center px-2" :class="{ 'justify-center': !isExpandSideBar }">
                <img id="logo-pc-id" x-bind:src="logo" alt="" class="w-12">
                <span x-show="isExpandSideBar" class="ml-2 text-lg font-semibold whitespace-nowrap">Личный кабинет</span>
            </div>
            <!-- Navigation menu -->
            @include('layouts.navigation-menu')
        </aside>
        <!-- Mobile sidebar -->
        <aside class="lg:hidden fixed w-80 h-full flex flex-col bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-400/20 transition-all duration-700 ease-in-out"
            :class="{ '-translate-x-full opacity-0': !isSideBarOpen, 'translate-x-0 opacity-100': isSideBarOpen }">
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
        <div class="flex-1 flex flex-col">
            <header class="shrink-0 w-full h-16 border-b border-slate-200 dark:border-slate-400/20">
                <div class="h-full flex justify-between px-5">
                    <!-- Desktop sideber -->
                    <button @click="toggleSideBar()" class="hidden lg:block">
                        <i class="fa-solid fa-bars fa-lg"></i>
                    </button>
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
            <main class="w-full overflow-x-hidden overflow-y-auto grow p-5 scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
                <div class="flex flex-col">
                    <div class="mb-4">
                        <h1 class="text-2xl font-semibold">{{ $header }}</h1>
                    </div>
                    {{ $slot }}
                </div>
            </main>
            <footer class="shrink-0 w-full h-14 flex items-center justify-center border-t border-slate-200 dark:border-slate-400/20">
                <span id="footer-id">foo</span>
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