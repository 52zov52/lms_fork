<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Асинхронное взаимодействие') }}</h1>
    </x-slot>

    @livewire('student.interact-with-teacher')
</x-app-layout>