<x-app-layout>
    <x-slot name="header">
        <h1>Управление мероприятиями по ВР и МП</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление мероприятиями по ВР и МП']
        ]" />
    </x-slot>

    @livewire('upbringing-management')
</x-app-layout>