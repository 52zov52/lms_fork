<x-app-layout>
    <x-slot name="header">
        <div class="flex space-x-4 items-center">
            @if(auth()->user()->userData->sex == 0)
            <img src="{{ asset('storage/profile-male.png') }}" alt="" class="w-16 h-16">
            @else
            <img src="{{ asset('storage/profile-female.png') }}" alt="" class="w-16 h-16">
            @endif
            <div class="flex flex-col">
                <h1>{{ auth()->user()->userData->last_name }} {{ auth()->user()->userData->first_name }}</h1>
                <p class="text-sm">@role('Student')Группа: @endrole {{ $additionalInfoAboutUser }}</p>
            </div>
        </div>
    </x-slot>

    @livewire('schedule')
</x-app-layout>