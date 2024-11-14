<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('График пересдач академических задолженностей') }}</h1>
    </x-slot>

    @livewire('student.academic-retake-schedule-list')
</x-app-layout>