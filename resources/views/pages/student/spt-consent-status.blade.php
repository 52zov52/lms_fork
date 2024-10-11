<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Согласие на социально-психологическое тестирование') }}</h1>
    </x-slot>

    @livewire('student.spt-consent-status')
</x-app-layout>