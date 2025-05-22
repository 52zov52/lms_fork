<x-app-layout>
    <x-slot name="header">
        <h1>Мои дисциплины</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Мои дисциплины']
        ]" />
    </x-slot>

    @livewire('student.my-disciplines')
</x-app-layout>