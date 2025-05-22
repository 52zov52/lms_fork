<x-app-layout>
    <?php
        if (auth()->user()->hasRole('Teacher')) {
            $url = 'teacher.async.interact.chats';
        } else {
            $url = 'student.async.interact.chats';
        }
    ?>

    <x-slot name="header">
        <h1>Просмотр учебного материала</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Асинхронное взаимодействие', 'url' => route($url)],
            ['title' => 'Просмотр учебного материала']
        ]" />
    </x-slot>

    @livewire('common.async-interact-chat-view-file', ['chatId' => $chatId])
</x-app-layout>