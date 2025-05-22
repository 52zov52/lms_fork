<x-app-layout>
    <?php
        if (auth()->user()->hasRole('Teacher')) {
            $url = 'teacher.async.interact.chats';
        } else {
            $url = 'student.async.interact.chats';
        }
    ?>
    
    <x-slot name="header">
        <h1>{{ __('Список чатов') }}</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Асинхронное взаимодействие', 'url' => route($url)],
            ['title' => 'Список чатов']
        ]" />
    </x-slot>

    @livewire('common.async-interact-chat-list', [
        'year' => $year,
        'halfYear' => $halfYear,
        'disciplineId' => $disciplineId,
        'normativeId' => $normativeId,
        'groupId' => $groupId
    ])
</x-app-layout>