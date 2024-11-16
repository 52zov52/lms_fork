<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('График пересдач академических задолженностей студентов') }}</h1>
    </x-slot>

    @livewire('teacher.academic-retake-schedule')
</x-app-layout>