<x-app-layout>
    <x-slot name="breadcrumb">
        <div class="mb-4 flex bg-white rounded p-2">
            <span class="text-sm xl:text-base text-blue-500 hover:underline"><a href="/">{{ __('Главная') }}</a></span>
            <span class="ml-1 mr-1 text-sm xl:text-base">/</span>
            <span class="text-sm xl:text-base text-blue-500 hover:underline"><a href="{{ route('academic.leave.calendar') }}">{{ __('Календарь академ. отпусков') }}</a></span>
            <span class="ml-1 mr-1 text-sm xl:text-base">/</span>
            <span class="text-sm xl:text-base">{{ __('Документы по академ. отпуску') }}</span>
        </div>
    </x-slot>

    <x-slot name="header">
        {{ __('Оформление документов по академическому отпуску') }}
    </x-slot>

    @livewire('documents-students-academic-leaves', ['user_id' => $user_id, 'group_id' => $group_id])
</x-app-layout>