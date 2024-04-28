<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="theme-id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ИУБиП|Digital') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="module">
        if (localStorage.getItem('theme') != null) {
            document.getElementById('theme-id').classList.add(localStorage.getItem('theme'));
        }
    </script>

    <!-- Styles -->
    @livewireStyles
</head>

<body>
    <div id="app" x-data="setting()" class="h-screen bg-white dark:bg-slate-800 text-gray-600 dark:text-white font-sans font-light antialiased">
        {{ $slot }}

        @livewireScripts
        @stack('scripts')

        <script>
            function setting() {
                return {
                    logo: (localStorage.getItem('theme') === 'dark') ? "{{ asset('storage/logo_dark.png') }}" : "{{ asset('storage/logo_light.png') }}",
                    isDarkMode: (localStorage.getItem('theme') === 'dark')
                }
            }
        </script>
    </div>
</body>

</html>