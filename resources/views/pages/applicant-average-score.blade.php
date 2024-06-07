<x-app-layout>
    <x-slot name="header">
        {{ __('Средний балл аттестата абитуриентов') }}
    </x-slot>

    @livewire('applicant-average-score')
</x-app-layout>