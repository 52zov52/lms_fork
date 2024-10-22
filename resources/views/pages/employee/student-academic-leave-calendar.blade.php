<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Календарь академических отпусков') }}</h1>
    </x-slot>

    @livewire('employee.student-academic-leave-calendar')
</x-app-layout>