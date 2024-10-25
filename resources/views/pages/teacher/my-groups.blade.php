<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Мои учебные группы') }}</h1>
    </x-slot>

    @livewire('teacher.my-groups')
</x-app-layout>