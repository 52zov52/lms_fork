<x-app-layout>
    <x-slot name="header">
        <h1>Ведомости</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Ведомости']
        ]" />
    </x-slot>

    @livewire('student-grades')
</x-app-layout>