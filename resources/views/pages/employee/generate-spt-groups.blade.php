<x-app-layout>
    <x-slot name="header">
        <h1>Генерация групп для СПТ</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Генерация групп для СПТ']
        ]" />
    </x-slot>

    @livewire('employee.generate-spt-groups')
</x-app-layout>