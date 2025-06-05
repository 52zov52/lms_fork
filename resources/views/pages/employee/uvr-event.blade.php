<x-app-layout>
    <x-slot name="header">
        <h1>Мероприятия по ВР и социализации</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Мероприятия по ВР и социализации']
        ]" />
    </x-slot>

    @livewire('employee.uvr-event')
</x-app-layout>