<x-app-layout>
    <x-slot name="header">
        <h1>Отчеты и аналитика</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Отчеты и аналитика']
        ]" />
    </x-slot>

    @livewire('report')
</x-app-layout>