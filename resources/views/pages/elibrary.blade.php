<x-app-layout>
    <x-slot name="header">
        <h1>Электронные библиотечные системы</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Электронные библиотечные системы']
        ]" />
    </x-slot>

    @livewire('elibrary')
</x-app-layout>