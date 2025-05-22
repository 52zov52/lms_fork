<x-app-layout>
    <x-slot name="header">
        <h1>Моя посещаемость</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Моя посещаемость']
        ]" />
    </x-slot>

    @livewire('student.my-attendance')
</x-app-layout>