<x-app-layout>
    <x-slot name="header">
        <h1>Асинхронное взаимодействие</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Асинхронное взаимодействие']
        ]" />
    </x-slot>

    @livewire('common.async-interact-chat')
</x-app-layout>