<x-app-layout>
    <x-slot name="header">
        {{ __('Заказ документов (справок)') }}
    </x-slot>

    @livewire('document-order-request')
</x-app-layout>