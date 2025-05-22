<x-app-layout>
    <x-slot name="header">
        <h1>Предстоящие события на период с {{ \Carbon\Carbon::now()->format('d.m.Y') }} по {{ \Carbon\Carbon::now()->addDays(3)->format('d.m.Y') }}</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Предстоящие события']
        ]" />
    </x-slot>

    @livewire('upcoming-events')
</x-app-layout>