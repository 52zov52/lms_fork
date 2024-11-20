<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Анкета "Семейный статус обучающегося"') }}</h1>
    </x-slot>

    @livewire('student.family-status')
</x-app-layout>