<x-app-layout>
    <x-slot name="header">
        {{ __('Студенты, направившие социальный паспорт на утверждение') }}
    </x-slot>

    @livewire('employee.student-social-passport-review')
</x-app-layout>