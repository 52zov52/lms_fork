<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Анкетирование') }}</h1>
    </x-slot>

    @livewire('questionnaire')
</x-app-layout>