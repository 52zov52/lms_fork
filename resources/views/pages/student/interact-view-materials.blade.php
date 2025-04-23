<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Просмотр учебных материалов') }}</h1>
    </x-slot>

    @livewire('student.interact-view-materials', ['id' => $id])
</x-app-layout>