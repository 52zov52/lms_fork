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
    <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app" x-data="setting()" class="main-conteiner">
        <!-- Desktop nav menu -->
        <aside class="aside-desktop">
            <!-- Menu -->
            @include('layouts.menu.nav-menu')
        </aside>

        <!-- Mobile nav menu -->
        <aside x-cloak :class="{ '-translate-x-full opacity-0': !isMobileMenuVisible, 'translate-x-0 opacity-100': isMobileMenuVisible }" class="aside-mobile">
            <!-- Menu -->
            @include('layouts.menu.nav-menu')
        </aside>

        <div class="w-full flex flex-col">
            <header class="h-12 shrink-0 border-b border-b-slate-400/20">
                <div class="h-full flex justify-between md:justify-end px-2">
                    <div class="md:hidden h-full flex items-center">
                        <button @click="isMobileMenuVisible = !isMobileMenuVisible" class="header-icon">
                            <i class="fa-solid fa-bars"></i>
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
                <div class="h-full flex flex-col space-y-2">
                    <div class="mb-4">
                        {{ $header }}
                    </div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>
    @stack('scripts')

    <script>
        function setting() {
            return {
                logo: (localStorage.getItem('theme') === 'dark') ? "{{ asset('storage/logo_dark.png') }}" : "{{ asset('storage/logo_light.png') }}",
                isDarkMode: (localStorage.getItem('theme') === 'dark'),
                isMobileMenuVisible: false,
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

        toastr.options = {
            "positionClass": "toast-top-right",
            "closeButton": true,
            "progressBar": true
        }
        
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-toast', (event) => {
                toastr[event.type](event.message)
            });
        });
    </script>
</body>

</html>