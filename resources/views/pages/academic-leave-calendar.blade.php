<x-app-layout>
    <x-slot name="header">
        {{ __('Календарь академических отпусков') }}
    </x-slot>

    @livewire('academic-leave-calendar')
</x-app-layout>