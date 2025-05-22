<x-app-layout>
    <x-slot name="header">
        <h1>Семейный статус обучающегося</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Анкетирование', 'url' => route('questionnaire')],
            ['title' => 'Семейный статус обучающегося']
        ]" />
    </x-slot>

    @livewire('student.family-status')
</x-app-layout>