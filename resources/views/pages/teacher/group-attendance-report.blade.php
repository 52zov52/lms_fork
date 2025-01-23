<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Отчет о посещаемости группы') }}</h1>
    </x-slot>

    @livewire('teacher.group-attendance-report', ['wspCoursesInSemesterId' => $wspCoursesInSemesterId, 'groupId' => $groupId])
</x-app-layout>