<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Мой список трудов') }}</h1>
    </x-slot>

    @livewire('my-publications')
</x-app-layout>