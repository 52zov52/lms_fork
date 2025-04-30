<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Список чатов') }}</h1>
    </x-slot>

    @livewire('common.async-interact-chat-list', [
        'year' => $year,
        'halfYear' => $halfYear,
        'disciplineId' => $disciplineId,
        'normativeId' => $normativeId,
        'groupId' => $groupId
    ])
</x-app-layout>