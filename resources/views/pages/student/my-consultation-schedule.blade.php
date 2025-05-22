<x-app-layout>
    <x-slot name="header">
        <h1>График консультаций</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Графики', 'url' => route('student.academic.retake.and.consultation.schedule')],
            ['title' => 'График консультаций']
        ]" />
    </x-slot>

    @livewire('student.my-consultation-schedule')
</x-app-layout>