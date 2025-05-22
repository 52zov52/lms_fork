<x-app-layout>
    <?php
        $header = ($mode == 1) ? 'Загрузка уч. материалов' : 'Чат';

        if (auth()->user()->hasRole('Teacher')) {
            $url = 'teacher.async.interact.chats';
        } else {
            $url = 'student.async.interact.chats';
        }
    ?>

    <x-slot name="header">
        <h1>{{ $header }}</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Асинхронное взаимодействие', 'url' => route($url)],
            ['title' => $header]
        ]" />
    </x-slot>

    @livewire('common.async-interact-chat-upload-file', ['chatId' => $chatId, 'mode' => $mode])
</x-app-layout>