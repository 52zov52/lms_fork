<x-app-layout>
    <x-slot name="header">
        <h1>Управление ВР и социализации</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление ВР и социализации']
        ]" />
    </x-slot>

    @livewire('employee.uvr-management')
</x-app-layout>