<x-app-layout>
    <x-slot name="header">
        <h1>Календарь академических отпусков</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Календарь академических отпусков']
        ]" />
    </x-slot>

    @livewire('employee.student-academic-leave-calendar')
</x-app-layout>