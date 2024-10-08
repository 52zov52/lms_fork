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

    <script type="module">
        if (localStorage.getItem('theme') != null) {
            document.getElementById('theme').classList.add(localStorage.getItem('theme'));
        }
    </script>

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div x-data="setting()" class="main-conteiner">
        <aside x-cloak :class="{'w-16': !isSideBarExpand, 'w-64': isSideBarExpand }" class="flex flex-col border-r border-r-slate-400/20 transition-all duration-500 ease-in-out">
            <div :class="{ 'justify-center': !isSideBarExpand, 'space-x-2 px-2': isSideBarExpand }" class="flex items-center py-2.5">
                <img id="logo" x-bind:src="logo" alt="" class="w-12 h-8">
                <span x-show="isSideBarExpand" 
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-0"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="font-semibold whitespace-nowrap">Личный кабинет</span>
            </div>
            <div class="mt-4">
                <a href="" class="">
                    <div :class="{ 'justify-center': !isSideBarExpand, 'space-x-2 px-2': isSideBarExpand }" class="h-8 flex items-center py-2.5">
                        <i class="fa-solid fa-house fa-lg"></i>
                        <span x-show="isSideBarExpand" 
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-0"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="font-semibold whitespace-nowrap">Главная</span>
                    </div>
                </a>
            </div>
        </aside>
        <div class="w-full flex flex-col">
            <header class="h-12 shrink-0 border-b border-b-slate-400/20">
                <div class="h-full flex justify-between px-2">
                    <div class="h-full flex items-center">
                        <button @click="isSideBarExpand = !isSideBarExpand" class="header-icon">
                            <template x-if="!isSideBarExpand">
                                <i class="fa-solid fa-chevron-right"></i>
                            </template>
                            <template x-if="isSideBarExpand">
                                <i class="fa-solid fa-chevron-left"></i>
                            </template>
                        </button>
                    </div>
                    <div class="h-full flex space-x-1 items-center">
                        <button @click="toggleDarkMode()" class="header-icon">
                            <template x-if="!isDarkMode">
                                <i class="fa-solid fa-sun"></i>
                            </template>
                            <template x-if="isDarkMode">
                                <i class="fa-solid fa-moon"></i>
                            </template>
                        </button>
                        <a href="#" class="header-icon">
                            <i class="fa-solid fa-bell"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <a @click.prevent="$root.submit();" href="{{ route('logout') }}" class="header-icon">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </form>
                        <div class="header-profile">
                            <span class="font-semibold">
                                {{ mb_substr(auth()->user()->userData->last_name, 0, 1) }}{{ mb_substr(auth()->user()->userData->first_name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>
            <main class="grow overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300 p-4">
                {{$slot}}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')

    <script>
        function setting() {
            return {
                logo: (localStorage.getItem('theme') === 'dark') ? "{{ asset('storage/logo_dark.png') }}" : "{{ asset('storage/logo_light.png') }}",
                isDarkMode: (localStorage.getItem('theme') === 'dark'),
                isMobileMenuVisible: false,
                isSideBarExpand: false,
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