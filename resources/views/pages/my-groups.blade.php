<x-app-layout>
    <x-slot name="header">
        {{ __('Мои группы') }}
    </x-slot>

    @livewire('my-groups')
</x-app-layout>