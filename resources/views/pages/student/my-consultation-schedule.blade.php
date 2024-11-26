<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('График консультаций') }}</h1>
    </x-slot>

    @livewire('student.my-consultation-schedule')
</x-app-layout>