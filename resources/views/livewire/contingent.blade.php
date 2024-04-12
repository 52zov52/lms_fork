<div class="flex flex-col space-y-2">
    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="groupId" wire:change="getContingent" name="group_id" id="group-id" class="w-full md:w-auto bg-white dark:bg-gray-700 border rounded border-slate-200 dark:border-slate-400/20 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
    @if(!is_null($contingent) && count($contingent) != 0)
        <div class="border rounded border-slate-200 dark:border-slate-400/20">
            <table class="w-full text-xs lg:text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 uppercase">
                    <tr>
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">ФИО</th>
                        <th scope="col" class="w-64 px-2 py-2 font-semibold">№ зачетки</th>
                        <th scope="col" class="w-64 px-2 py-2 font-semibold">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contingent as $item)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 border-b dark:border-gray-700">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto px-2 py-2 ">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                            <td class="w-64 px-2 py-2 text-center">{{ $item->ticket_number }}</td>
                            <td class="w-64 px-2 py-2">{{ $item->state_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-alert type="info" title="Информация!" message="Информация отсутствует!" />
    @endif
</div>