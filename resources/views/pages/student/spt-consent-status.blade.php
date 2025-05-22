<x-app-layout>
    <x-slot name="header">
        <h1>Согласие на социально-психологическое тестирование</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Согласие на социально-психологическое тестирование']
        ]" />
    </x-slot>

    @livewire('student.spt-consent-status')
</x-app-layout>