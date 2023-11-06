<x-app-layout>
    <x-slot name="header">
        {{ __('Форма обратной связи') }}
    </x-slot>

    @livewire('feedback-message')
</x-app-layout>