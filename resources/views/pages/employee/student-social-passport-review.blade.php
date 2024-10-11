<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Студенты, направившие социальный паспорт на утверждение') }}</h1>
    </x-slot>

    @livewire('employee.student-social-passport-review')
</x-app-layout>