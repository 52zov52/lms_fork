<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Список учебных дисциплин') }}</h1>
    </x-slot>

    @livewire('teacher.course-list', ['groupId' => $groupId])
</x-app-layout>