<div class="flex flex-col space-y-2">

    @if(session()->has('message-success') && $isAlertVisible)
    <div class="flex items-center justify-between bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                {{ session('message-success') }}
            </div>
        </div>
        <button wire:click="alertVisible"><i class="fa-solid fa-xmark fal-lg"></i></button>
    </div>
    @endif

    <div class="flex flex-row space-x-1">
        <span class="bg-purple-600 rounded px-2 py-1 text-sm text-white">Лекции</span>
        <span class="bg-sky-600 rounded px-2 py-1 text-sm text-white">Лабораторные</span>
        <span class="bg-yellow-600 rounded px-2 py-1 text-sm text-white">Практические</span>
    </div>

    <div class="flex flex-row space-x-1">
        <span class="bg-green-600 rounded px-2 py-1 text-sm text-white">
            <label for="" class="flex items-center">
                <input class="mr-2" type="checkbox" checked disabled readonly>Присутствовал
            </label>
        </span>
        <span class="bg-red-600 rounded px-2 py-1 text-sm text-white">
            <label for="" class="flex items-center">
                <input class="mr-2" type="checkbox" disabled readonly>Отсутствовал
            </label>
        </span>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
            <table class="w-auto bg-white shadow-md border text-xs">
                <tbody>
                    <tr class="z-10 sticky top-0 bg-gray-100 border">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="sticky left-0 w-auto border bg-gray-100 px-2 py-2 font-semibold">ФИО</td>
                        <td class="w-20 border px-2 py-2 text-center font-semibold">№ зачетки</td>
                        @foreach($dates as $dateAndTime)
                        <td class="w-20 border border-t-4 border-r-4
                            @if($dateAndTime->lesson_type == 1) 
                                border-t-purple-600 
                            @elseif($dateAndTime->lesson_type == 2) 
                                border-t-sky-600
                            @else 
                                border-t-yellow-600
                            @endif
                            px-2 py-2 text-center font-semibold">
                            {{ $dateAndTime->lesson_date }} {{ $dateAndTime->lesson_time }}
                        </td>
                        @endforeach
                        <td class="w-20 border px-2 py-2 text-center font-semibold">
                            {{ $currentData }} {{ $currentTime }}

                            <button wire:click="store" class="w-6 bg-green-500 hover:bg-green-600 rounded p-1 text-white">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <button wire:click="edit" class="w-6 bg-sky-500 hover:bg-sky-600 rounded p-1 text-white">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </td>
                    </tr>
                    @foreach($contingent as $key => $value)
                    <tr class="border">
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="z-0 sticky left-0 w-auto bg-white border px-2 py-2">{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</td>
                        <td class="w-20 border px-2 py-2 text-center">{{ $value->ticket_number }}</td>
                        @foreach($journal as $item)
                        @if($value->user_id == $item->student_id)
                        <td class="w-20 border px-2 py-2 text-center">
                            <input wire:model="status.{{ $item->id }}" wire:change="setStatus({{ $item->id }}, {{ $value->user_id }})" type="checkbox" id="status-{{ $item->id }}">
                        </td>
                        @endif
                        @endforeach
                        <td class="w-20 border px-2 py-2 text-center"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>