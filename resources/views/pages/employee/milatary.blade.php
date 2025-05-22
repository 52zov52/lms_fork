<x-app-layout>
    <x-slot name="header">
        <h1>Воинский учёт</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Воинский учёт']
        ]" />
    </x-slot>

    @livewire('employee.milatary')
</x-app-layout>