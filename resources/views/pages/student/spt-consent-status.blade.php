<x-app-layout>
    <x-slot name="header">
        {{ __('Согласие на социально-психологическое тестирование') }}
    </x-slot>

    @livewire('student.spt-consent-status')
</x-app-layout>