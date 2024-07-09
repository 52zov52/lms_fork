<x-app-layout>
    <x-slot name="header">
        {{ __('Ведомость') }}
    </x-slot>

    @livewire('bulletin', ['bulletin_id' => $bulletin_id])
</x-app-layout>