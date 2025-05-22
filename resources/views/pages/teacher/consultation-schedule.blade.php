<x-app-layout>
    <x-slot name="header">
        <h1>График консультаций студентов</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'График консультаций студентов']
        ]" />
    </x-slot>

    @livewire('teacher.consultation-schedule')
</x-app-layout>