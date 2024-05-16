<x-app-layout>
    <x-slot name="header">
        {{ __('Мой список трудов') }}
    </x-slot>

    @livewire('my-publications')
</x-app-layout>