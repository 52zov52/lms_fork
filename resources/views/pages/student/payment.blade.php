<x-app-layout>
    <x-slot name="header">
        <h1>Оплата</h1>
    </x-slot>
    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Оплата']
        ]" />
    </x-slot>
    <x-alert type="info" title="Оплата" message="Раздел в разработке" />
</x-app-layout>