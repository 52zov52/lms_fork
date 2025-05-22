<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Отчет о посещаемости группы') }}</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Мои учебные группы', 'url' => route('teacher.my.groups')],
            ['title' => 'Список учебных дисциплин', 'url' => route('teacher.course.list', $groupId)],
            ['title' => 'Отчет о посещаемости группы']
        ]" />
    </x-slot>

    @livewire('teacher.group-attendance-report', ['wspCoursesInSemesterId' => $wspCoursesInSemesterId, 'groupId' => $groupId])
</x-app-layout>