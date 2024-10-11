<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Моя успеваемость') }}</h1>
    </x-slot>

    @livewire('student.my-grades')
</x-app-layout>