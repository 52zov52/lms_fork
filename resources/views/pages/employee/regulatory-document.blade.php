<x-app-layout>
    <x-slot name="header">
        <h1>Справочник нормативных документов</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Справочник нормативных документов']
        ]" />
    </x-slot>

    @livewire('employee.regulatory-document')
</x-app-layout>