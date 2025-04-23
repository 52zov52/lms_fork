<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Загрузка учебных материалов для занятия') }}</h1>
    </x-slot>

    @livewire('teacher.interact-upload-materials', ['id' => $id])
</x-app-layout>