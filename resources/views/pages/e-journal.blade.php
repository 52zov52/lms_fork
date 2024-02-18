<x-app-layout>
    <x-slot name="breadcrumb">
        <div class="mb-4 flex bg-white rounded p-2">
            <span class="text-sm xl:text-base text-blue-500 hover:underline"><a href="/">{{ __('Главная') }}</a></span>
            <span class="ml-1 mr-1 text-sm xl:text-base">/</span>
            <span class="text-sm xl:text-base text-blue-500 hover:underline"><a href="{{ route('my.groups') }}">{{ __('Мои группы') }}</a></span>
            <span class="ml-1 mr-1 text-sm xl:text-base">/</span>
            <span class="text-sm xl:text-base">{{ __('Журнал') }}</span>
        </div>
    </x-slot>

    <x-slot name="header">
        {{ __('Учёт посещаемости занятий и выполнения учебных заданий') }}
    </x-slot>

    @livewire('e-journal', ['courseTeacherId' => $courseTeacherId])
</x-app-layout>