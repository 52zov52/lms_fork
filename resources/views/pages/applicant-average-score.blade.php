<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Средний балл аттестата абитуриентов') }}</h1>
    </x-slot>

    @livewire('applicant-average-score')
</x-app-layout>