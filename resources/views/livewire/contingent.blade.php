<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    @if(session()->has('message-error'))
    <div class="flex items-center bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-error') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="groupId" wire:change="getContingent" name="group_id" id="group-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите группу из списка</option>
            @foreach($groups as $group)
            <option value="{{ $group->id }}">{{ $group->group_name }}</option>
            @endforeach
        </select>
        <button wire:click="generateListOfStudentsInGroupReport" class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-file-excel"></i>
            Скачать список студентов
        </button>
    </div>
    <div class="bg-white rounded shadow-xl p-4">
        @if(!is_null($contingent) && count($contingent) != 0)
        <table class="w-full bg-white border shadow-md text-xs">
            <tbody>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                    <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                    <td class="w-64 border px-2 py-2 font-semibold">№ зачетки</td>
                    <td class="w-64 border px-2 py-2 font-semibold">Статус</td>
                </tr>
                @foreach($contingent as $item)
                <tr>
                    <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto border px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                    <td class="w-64 border px-2 py-2">{{ $item->ticket_number }}</td>
                    <td class="w-64 border px-2 py-2">{{ $item->state_name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует. Для отображения данных выберите группу из списка.
            </div>
        </div>
        @endif
    </div>
</div>