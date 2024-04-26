<x-app-layout>
    <x-slot name="header">
        {{ __('Учёт посещаемости занятий и выполнения учебных заданий') }}
    </x-slot>

    @livewire('e-journal', ['courseTeacherId' => $courseTeacherId])
</x-app-layout>