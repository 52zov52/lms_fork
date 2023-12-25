<x-app-layout>
    <x-slot name="breadcrumb">
    </x-slot>
    
    <x-slot name="header">
        {{ __('Главная') }}
    </x-slot>

    @livewire('schedule')
</x-app-layout>