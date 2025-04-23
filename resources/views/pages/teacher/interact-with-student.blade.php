<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Асинхронное взаимодействие') }}</h1>
    </x-slot>

    @livewire('teacher.interact-with-student')
</x-app-layout>