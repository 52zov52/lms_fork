<x-app-layout>
    <x-slot name="header">
        <h1>Решение ученого совета</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Решение ученого совета']
        ]" />
    </x-slot>

    @livewire('decisions-council')
</x-app-layout>