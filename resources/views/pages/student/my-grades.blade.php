<x-app-layout>
    <x-slot name="header">
        <h1>Моя успеваемость</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Моя успеваемость']
        ]" />
    </x-slot>

    @livewire('student.my-grades')
</x-app-layout>