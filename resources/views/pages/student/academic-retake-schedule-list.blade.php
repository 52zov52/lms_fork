<x-app-layout>
    <x-slot name="header">
        <h1>График пересдач академических задолженностей</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Графики', 'url' => route('student.academic.retake.and.consultation.schedule')],
            ['title' => 'График пересдач академических задолженностей']
        ]" />
    </x-slot>

    @livewire('student.academic-retake-schedule-list')
</x-app-layout>