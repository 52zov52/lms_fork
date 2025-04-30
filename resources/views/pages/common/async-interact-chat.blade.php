<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Асинхронное взаимодействие') }}</h1>
    </x-slot>

    @livewire('common.async-interact-chat')
</x-app-layout>