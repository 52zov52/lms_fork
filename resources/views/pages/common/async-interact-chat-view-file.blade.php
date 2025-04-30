<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Просмотр учебного материала') }}</h1>
    </x-slot>

    @livewire('common.async-interact-chat-view-file', ['chatId' => $chatId])
</x-app-layout>