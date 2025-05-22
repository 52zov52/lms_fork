<x-app-layout>
    <x-slot name="header">
        <h1>Электронный журнал</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Мои учебные группы', 'url' => route('teacher.my.groups')],
            ['title' => 'Список учебных дисциплин', 'url' => route('teacher.course.list', $groupId)],
            ['title' => 'Электронный журнал']
        ]" />
    </x-slot>

    @livewire('teacher.e-journal', ['wspCoursesInSemesterId' => $wspCoursesInSemesterId, 'groupId' => $groupId])
</x-app-layout>