<x-app-layout>
    <x-slot name="header">
        <h1>{{ ($mode == 1) ? 'Загрузка уч. материалов' : 'Чат' }}</h1>
    </x-slot>

    @livewire('common.async-interact-chat-upload-file', ['chatId' => $chatId, 'mode' => $mode])
</x-app-layout>