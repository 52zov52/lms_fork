<x-app-layout>
    <x-slot name="header">
        <h1>Графики</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Графики']
        ]" />
    </x-slot>

    @livewire('student.academic-retake-and-consultation-schedule')
</x-app-layout>