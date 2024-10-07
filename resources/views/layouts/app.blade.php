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

    </style>

    <script type="module">
        if (localStorage.getItem('theme') != null) {
            document.getElementById('theme').classList.add(localStorage.getItem('theme'));
        }
        document.getElementById('footer').innerHTML = "ЧОУ ВО ЮУ (ИУБиП) © 2023 - " + new Date().getFullYear();
    </script>

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div x-data="setting()" class="main-conteiner">
        <aside x-cloak :class="{'w-16': !isSideBar, 'w-64': isSideBar }" class="bg-red-100 transition-all duration-500 ease-in-out">
            <button @click="isSideBar = !isSideBar" class="text-gray-600">OK</button>
        </aside>
        <div class="w-full flex flex-col">
            <header class="h-16 shrink-0 bg-green-100">header</header>
            <main class="grow bg-blue-100 overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
                {{$slot}}
            </main>
            <footer class="h-16 shrink-0 bg-orange-100" id="footer"></footer>
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
                isSideBar: false,
                toggleDarkMode() {
                    this.isDarkMode = !this.isDarkMode;
                    localStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');

                    if (this.isDarkMode) {
                        document.getElementById("logo-pc").src = "{{ asset('storage/logo_dark.png') }}";

                        document.getElementById('theme').classList.remove('light');
                        document.getElementById('theme').classList.add('dark');
                    } else {
                        document.getElementById("logo-pc").src = "{{ asset('storage/logo_light.png') }}";

                        document.getElementById('theme').classList.remove('dark');
                        document.getElementById('theme').classList.add('light');
                    }
                }
            }
        }
    </script>
</body>

</html>