<x-app-layout>
    <x-slot name="header">
        {{ __('Управление проектами') }}
    </x-slot>

    @livewire('project-board')
</x-app-layout>