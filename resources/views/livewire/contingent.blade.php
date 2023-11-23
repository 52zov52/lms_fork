<div class="flex flex-col space-y-2">
    <div class="w-full">
        <select wire:model="groupId" wire:change="getContingent" name="group_id" id="group-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите группу из списка</option>
            @foreach($groups as $group)
            <option value="{{ $group->id }}">{{ $group->group_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!is_null($contingent) && count($contingent) != 0)
        <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
            <tbody>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                    <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                    <td class="w-auto border px-2 py-2 font-semibold">№ зачетки</td>
                </tr>
                @foreach($contingent as $item)
                <tr>
                    <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto border px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                    <td class="w-auto border px-2 py-2">{{ $item->ticket_number }}</td>
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