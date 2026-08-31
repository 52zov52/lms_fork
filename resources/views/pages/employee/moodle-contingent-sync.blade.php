<x-app-layout>
    <x-slot name="header">
        <h1>Синхронизация контингента Moodle</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Синхронизация с Moodle']
        ]" />
    </x-slot>

    @livewire('employee.moodle-contingent-sync')
</x-app-layout>