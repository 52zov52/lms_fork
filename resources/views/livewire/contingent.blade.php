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
        <div class="tab-wrapper px-2 py-4" x-data="{ activeTab:  0 }">
            <ul class="flex bg-white text-sm md:text-base lg:text-base xl:text-base font-medium text-center divide-x divide-gray-200 border rounded-lg shadow-md">
                <li class="w-full">
                    <a href="#" @click="activeTab = 0" :class="{ 'active bg-gray-100': activeTab === 0 }" class="inline-block h-full w-full p-4 rounded-l-lg hover:bg-gray-100" aria-current="page">Списочный состав групп</a>
                </li>
                <li class="w-full">
                    <a href="#" @click="activeTab = 1" :class="{ 'active bg-gray-100': activeTab === 1 }" class="inline-block h-full w-full p-4 rounded-r-lg hover:bg-gray-100" aria-current="page">В академическом отпуске</a>
                </li>
            </ul>

            <div class="tab-panel mt-4" :class="{ 'active': activeTab === 0 }" x-show.transition.in.opacity.duration.600="activeTab === 0">
                @if(!is_null($contingent) && count($contingent) != 0)
                <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
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

            <div class="tab-panel mt-4" :class="{ 'active': activeTab === 1 }" x-show.transition.in.opacity.duration.600="activeTab === 1">
                <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                    <tbody>
                        <tr class="bg-gray-100">
                            <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                            <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Группа</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Дата выхода из академ. отпуска</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Комментарий</td>
                            <td>Действия</td>
                        </tr>
                        @foreach($studentsAcademicLeaves as $student)
                        <tr>
                            <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto border px-2 py-2">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                            <td class="w-64 border px-2 py-2">{{ $student->group_name }}</td>
                            <td class="w-64 border px-2 py-2">{{ \Carbon\Carbon::parse($student->academic_leave_end_date)->format('d.m.Y') }}</td>
                            <td class="w-64 border px-2 py-2">{{ $student->description }}</td>
                            <td><button wire:click="spravka301({{ $student->id }})">print 301</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>