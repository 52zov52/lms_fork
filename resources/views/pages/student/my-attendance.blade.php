<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Моя посещаемость') }}</h1>
    </x-slot>

    @livewire('student.my-attendance')
</x-app-layout>