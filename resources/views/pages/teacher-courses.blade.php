<x-app-layout>
    <x-slot name="header">
        {{ __('Список моих дисциплин (модулей) в текущем уч. году') }}
    </x-slot>

    @livewire('teacher-courses', ['groupId' => $groupId])
</x-app-layout>