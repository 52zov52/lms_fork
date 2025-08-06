<x-app-layout>
    <x-slot name="header">
        <h1>Сведения о воинском учете</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Сведения о воинском учете']
        ]" />
    </x-slot>

    @livewire('student.military-registration')
</x-app-layout>