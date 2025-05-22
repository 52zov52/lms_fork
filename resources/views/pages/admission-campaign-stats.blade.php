<x-app-layout>
    <x-slot name="header">
        <h1>Ввод статистики приемной кампании</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Приемная кампания', 'url' => route('admission.campaign')],
            ['title' => 'Ввод статистики приемной кампании']
        ]" />
    </x-slot>

    @livewire('admission-campaign-stats')
</x-app-layout>