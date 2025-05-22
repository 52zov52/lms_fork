<x-app-layout>
    <x-slot name="header">
        <h1>Цифровой профиль преподавателя</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя']
        ]" />
    </x-slot>

    @livewire('teacher.digital-profile')
</x-app-layout>