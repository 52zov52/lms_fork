<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('График консультаций студентов') }}</h1>
    </x-slot>

    @livewire('teacher.consultation-schedule')
</x-app-layout>