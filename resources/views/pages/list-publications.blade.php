<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Список научных трудов сотрудников Университета') }}</h1>
    </x-slot>

    @livewire('list-publications')
</x-app-layout>