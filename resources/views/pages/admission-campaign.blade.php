<x-app-layout>
    <x-slot name="header">
        <h1>Приемная кампания</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Приемная кампания']
        ]" />
    </x-slot>

    @livewire('admission-campaign')
</x-app-layout>