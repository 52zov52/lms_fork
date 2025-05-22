<x-app-layout>
    <x-slot name="header">
        <h1>Список научных трудов сотрудников Университета</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Список научных трудов сотрудников Университета']
        ]" />
    </x-slot>

    @livewire('list-publications')
</x-app-layout>