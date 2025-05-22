<x-app-layout>
    <x-slot name="header">
        <h1>Контингент</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Контингент']
        ]" />
    </x-slot>

    @livewire('employee.contingent')
</x-app-layout>