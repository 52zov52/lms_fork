<x-app-layout>
    <x-slot name="breadcrumb">
        <div class="mb-4 flex bg-white rounded p-2">
            <span class="text-sm xl:text-base text-blue-500 hover:underline"><a href="/">{{ __('Главная') }}</a></span>
            <span class="ml-1 mr-1 text-sm xl:text-base">/</span>
            <span class="text-sm xl:text-base">{{ __('Отчеты и аналитика') }}</span>
        </div>
    </x-slot>

    <x-slot name="header">
        {{ __('Отчеты и аналитика') }}
    </x-slot>

    @livewire('report')
</x-app-layout>