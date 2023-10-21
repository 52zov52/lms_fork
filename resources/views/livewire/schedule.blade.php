<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif
    <button wire:click="uploadSchedule" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">Обновить расписание</button>

</div>