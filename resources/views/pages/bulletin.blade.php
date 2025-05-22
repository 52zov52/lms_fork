<x-app-layout>
    <x-slot name="header">
        <h1>Ведомость успеваемости</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Ведомости', 'url' => route('student.grades')],
            ['title' => 'Ведомость успеваемости']
        ]" />
    </x-slot>

    @livewire('bulletin', ['bulletin_id' => $bulletin_id])
</x-app-layout>