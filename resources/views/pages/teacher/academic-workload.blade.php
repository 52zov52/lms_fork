<x-app-layout>
    <x-slot name="header">
        <h1>Учебная нагрузка</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Учебная нагрузка']
        ]" />
    </x-slot>

    @livewire('teacher.academic-workload')
</x-app-layout>