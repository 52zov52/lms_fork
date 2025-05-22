<x-app-layout>
    <x-slot name="header">
        <h1>Социальные паспорта студентов</h1>
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[
            ['title' => 'Главная', 'url' => '/'],
            ['title' => 'Социальные паспорта студентов']
        ]" />
    </x-slot>

    @livewire('employee.student-social-passport-review')
</x-app-layout>