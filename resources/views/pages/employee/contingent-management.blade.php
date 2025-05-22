<x-app-layout>
    <x-slot name="header">
        <h1>Управление контингентом</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом']
        ]" />
    </x-slot>

    @livewire('employee.contingent-management')
</x-app-layout>