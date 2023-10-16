<x-app-layout>
    <x-slot name="header">
        {{ __('Заказ документов') }}
    </x-slot>

    @livewire('document-order-request')
</x-app-layout>