<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Заказ документов') }}</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Заказ документов']
        ]" />
    </x-slot>

    @livewire('student-document-requests')
</x-app-layout>