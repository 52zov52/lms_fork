<x-app-layout>
    <x-slot name="header">
        <h1>Обработка заявок на документы</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Управление контингентом', 'url' => route('employee.contingent.management')],
            ['title' => 'Обработка заявок на документы']
        ]" />
    </x-slot>

    @livewire('employee.student-document-order-issuances')
</x-app-layout>