<x-app-layout>
    <x-slot name="header">
        {{ __('Анкета "Социальный паспорт обучающегося"') }}
    </x-slot>

    @livewire('student.social-passport')
</x-app-layout>