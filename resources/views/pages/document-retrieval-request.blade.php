<x-app-layout>
    <x-slot name="header">
        {{ __('Заявки на документы (справки)') }}
    </x-slot>

    @livewire('document-retrieval-request')
</x-app-layout>