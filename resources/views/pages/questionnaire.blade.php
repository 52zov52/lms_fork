<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Анкетирование') }}</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Анкетирование']
        ]" />
    </x-slot>

    @livewire('questionnaire')
</x-app-layout>