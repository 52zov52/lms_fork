<x-app-layout>
    <x-slot name="header">
        {{ __('Моя успеваемость') }}
    </x-slot>

    @livewire('student.my-grades')
</x-app-layout>