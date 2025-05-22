<x-app-layout>
    <x-slot name="header">
        <h1>Мои учебные группы</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Мои учебные группы']
        ]" />
    </x-slot>

    @livewire('teacher.my-groups')
</x-app-layout>