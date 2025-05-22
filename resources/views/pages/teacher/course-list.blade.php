<x-app-layout>
    <x-slot name="header">
        <h1>Список учебных дисциплин</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Цифровой профиль преподавателя', 'url' => route('teacher.digital.profile')],
            ['title' => 'Мои учебные группы', 'url' => route('teacher.my.groups')],
            ['title' => 'Список учебных дисциплин']
        ]" />
    </x-slot>

    @livewire('teacher.course-list', ['groupId' => $groupId])
</x-app-layout>