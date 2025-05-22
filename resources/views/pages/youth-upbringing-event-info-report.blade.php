<x-app-layout>
    <x-slot name="header">
        <h1>Мероприятия по ВР и МП</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Мероприятия по ВР и МП']
        ]" />
    </x-slot>

    @livewire('youth-upbringing-event-info-report')
</x-app-layout>