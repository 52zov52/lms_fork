<x-app-layout>
    <x-slot name="header">
        <h1>Решения ректората</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Решения ректората']
        ]" />
    </x-slot>

    @livewire('employee.rectorate-decision')
</x-app-layout>