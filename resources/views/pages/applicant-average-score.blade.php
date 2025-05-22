<x-app-layout>
    <x-slot name="header">
        <h1>Средний балл аттестата абитуриентов</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Приемная кампания', 'url' => route('admission.campaign')],
            ['title' => 'Средний балл аттестата абитуриентов']
        ]" />
    </x-slot>

    @livewire('applicant-average-score')
</x-app-layout>