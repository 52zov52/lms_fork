<x-app-layout>
    <x-slot name="header">
        <h1>Мой список трудов</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Мой список трудов']
        ]" />
    </x-slot>

    @livewire('my-publications')
</x-app-layout>