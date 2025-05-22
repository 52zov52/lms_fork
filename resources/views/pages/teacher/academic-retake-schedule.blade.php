<x-app-layout>
    <x-slot name="header">
        <h1>График пересдач академических задолженностей студентов</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'График пересдач академических задолженностей студентов']
        ]" />
    </x-slot>

    @livewire('teacher.academic-retake-schedule')
</x-app-layout>