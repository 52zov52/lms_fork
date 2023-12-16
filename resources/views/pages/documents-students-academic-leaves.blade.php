<x-app-layout>
    <x-slot name="header">
        {{ __('Оформление документов по академическому отпуску') }}
    </x-slot>

    @livewire('documents-students-academic-leaves', ['user_id' => $user_id, 'group_id' => $group_id])
</x-app-layout>