<x-app-layout>
    <x-slot name="header">
        <h1>Социальный паспорт обучающегося</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Анкетирование', 'url' => route('questionnaire')],
            ['title' => 'Социальный паспорт обучающегося']
        ]" />
    </x-slot>

    @livewire('student.social-passport')
</x-app-layout>