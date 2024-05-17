<x-app-layout>
    <x-slot name="header">
        {{ __('Список научных трудов сотрудников Университета') }}
    </x-slot>

    @livewire('list-publications')
</x-app-layout>