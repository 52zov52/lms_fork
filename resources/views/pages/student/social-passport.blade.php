<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Анкета "Социальный паспорт обучающегося"') }}</h1>
    </x-slot>

    @livewire('student.social-passport')
</x-app-layout>