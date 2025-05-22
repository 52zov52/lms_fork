<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col space-y-4">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="flex flex-row space-x-2 bg-blue-500 dark:bg-slate-700 border border-blue-500 rounded p-4">
                    <div class="w-12 shrink-0 items-center justify-center bg-blue-400 dark:bg-slate-700 border border-blue-400 rounded text-lg font-semibold text-white">
                        <div class="w-full h-full flex items-center justify-center">{{ $eventCount }}</div>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <p class="font-semibold text-white"><a href="{{ route('upcoming.event') }}" class="hover:underline">Предстоящие события</a></p>
                        <p class="text-xs text-white">{{ $event }}</p>
                    </div>
                </div>
                <div class="flex flex-row space-x-2 bg-sky-500 dark:bg-slate-700 border border-sky-500 rounded p-4">
                    <div class="w-12 shrink-0 items-center justify-center bg-sky-400 dark:bg-slate-700 border border-sky-400 rounded text-lg font-semibold text-white">
                        <div class="w-full h-full flex items-center justify-center">0</div>
                    </div>
                    <div class="flex flex-col">
                        <p class="font-semibold text-white">Объявления</p>
                        <p class="text-white">-</p>
                    </div>
                </div>
                <div class="flex flex-row space-x-2 bg-violet-500 dark:bg-slate-700 border border-violet-500 rounded p-4">
                    <div class="w-12 shrink-0 items-center justify-center bg-violet-400 dark:bg-slate-700 border border-violet-400 rounded text-lg font-semibold text-white">
                        <div class="w-full h-full flex items-center justify-center">0</div>
                    </div>
                    <div class="flex flex-col">
                        <p class="font-semibold text-white">Сообщения</p>
                        <p class="text-white">-</p>
                    </div>
                </div>
            </div>
            @role('Teacher')
                @if(!is_null($currentConference))
                <div class="flex items-center space-x-2 border border-slate-400/20 rounded p-4">
                    <i class="fa-regular fa-circle-dot text-red-500"></i>
                    <span class="flex">У вас есть активная видеоконференция.</span>
                    <a href="{{ $currentConference->link }}" target="_blank" rel="noopener noreferrer" class="text-green-500 hover:underline">Перейти в ВКС</a>
                    <a href="{{ route('logout.video.conference', $currentConference->id) }}" class="text-red-500 hover:underline">Завершить ВКС</a>
                </div>
                @endif
            @endrole
        </div>
    </x-slot>

    <x-slot name="breadcrumbs">
    </x-slot>

    @livewire('schedule')
</x-app-layout>