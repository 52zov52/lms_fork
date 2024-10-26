<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Журнал') }}</h1>
    </x-slot>

    @livewire('teacher.e-journal', ['wspCoursesInSemesterId' => $wspCoursesInSemesterId, 'groupId' => $groupId])
</x-app-layout>