<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Предстоящие события') }}</h1>
    </x-slot>

    @livewire('upcoming-events')
</x-app-layout>