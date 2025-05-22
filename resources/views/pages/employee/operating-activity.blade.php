<x-app-layout>
    <x-slot name="header">
        <h1>Операционная деятельность</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Операционная деятельность']
        ]" />
    </x-slot>

    @livewire('employee.operating-activity')
</x-app-layout>