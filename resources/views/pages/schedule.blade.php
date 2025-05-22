<x-app-layout>
    <x-slot name="header">
        <h1>Расписание</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Расписание']
        ]" />
    </x-slot>

    @livewire('schedule')
</x-app-layout>