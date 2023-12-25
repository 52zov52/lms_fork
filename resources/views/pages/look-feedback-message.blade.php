<x-app-layout>
    <x-slot name="header">
        {{ __('Форма просмотра сообщений обратной связи') }}
    </x-slot>

    @livewire('look-feedback-message', ['id' => $id])
</x-app-layout>