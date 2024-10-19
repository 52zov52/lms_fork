<x-app-layout>
    <x-slot name="header">
        <h1>{{ __('Обработка заявок на документы') }}</h1>
    </x-slot>

    @livewire('employee.student-document-order-issuances')
</x-app-layout>