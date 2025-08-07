<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-between">
            <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                <select wire:model="selectedGroupId" wire:change="getContingent" name="selectedGroupId" id="group-id" class="w-full md:w-auto form-select">
                    <option value="0">Выберите группу из списка</option>
                    @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                <button wire:click="downloadListOfStudents" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-2 text-xs text-left text-sky-400 hover:text-white font-bold">
                    <span wire:loading.remove wire:target="downloadListOfStudents">
                        <i class="fa-solid fa-download"></i> Скачать список студентов
                    </span>
                    <span wire:loading wire:target="downloadListOfStudents">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i> Идет загрузка
                    </span>
                </button>
                <button wire:click="downloadGroupStudentGrade" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-2 text-xs text-left text-sky-400 hover:text-white font-bold">
                    <span wire:loading.remove wire:target="downloadGroupStudentGrade">
                        <i class="fa-solid fa-download"></i> Скачать групповую сводку успеваемости
                    </span>
                    <span wire:loading wire:target="downloadGroupStudentGrade">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i> Идет загрузка
                    </span>
                </button>
            </div>
        </div>

        <div class="flex flex-row space-x-2">
            <input wire:model="ticketNumber" type="text" name="ticketNumber" id="ticket-number" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 px-2 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-600 dark:placeholder-white" placeholder="Для поиска введите номер личного дела (номер зачетной книжки)">
            <button wire:click="searchContingentByTicketNumber" class="w-12 bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-1 text-xs text-center text-blue-400 hover:text-white">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <div wire:loading.remove wire:target="getContingent, searchContingentByTicketNumber">
            @if(!is_null($contingent) && count($contingent) != 0)
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs md:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                            <th scope="col" class="w-64 px-2 py-2 font-semibold">Группа</th>
                            <th scope="col" class="w-64 px-2 py-2 font-semibold">№ зачетки</th>
                            <th scope="col" class="w-64 px-2 py-2 font-semibold">Статус воинского учета</th>
                            <th scope="col" class="w-24 px-2 py-2 font-semibold">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contingent as $item)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                            <td class="w-64 px-2 py-2 text-center">{{ $item->group_name }}</td>
                            <td class="w-64 px-2 py-2 text-center">{{ $item->ticket_number }}</td>
                            <td class="w-64 px-2 py-2 text-center">
                                @if(is_null($item->military_status))
                                не заполнен
                                @else
                                заполнен
                                @endif
                            </td>
                            <td class="w-24 px-2 py-2 text-center">
                                <button wire:click="downloadIndividualStudentGrade({{ $item->user_id }})" class="w-9 btn-download" title="" data-tippy-content="Скачать индивидуальную ведомость">
                                    <span wire:loading.remove wire:target="downloadIndividualStudentGrade({{ $item->user_id }})">
                                        <i class="fa-solid fa-download"></i>
                                    </span>
                                    <span wire:loading wire:target="downloadIndividualStudentGrade({{ $item->user_id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($contingent as $item)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">ФИО:</span>
                        <span>{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Группа:</span>
                        <span>{{ $item->group_name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">№ зачетки:</span>
                        <span>{{ $item->ticket_number }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Статус воинского учета:</span>
                        <span>
                            @if(is_null($item->military_status))
                            не заполнен
                            @else
                            заполнен
                            @endif
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Действия:</span>
                        <div class="flex flex-row space-x-2">
                            <button wire:click="downloadIndividualStudentGrade({{ $item->user_id }})" class="w-auto grow btn-download" title="" data-tippy-content="Скачать индивидуальную ведомость">
                                <span wire:loading.remove wire:target="downloadIndividualStudentGrade({{ $item->user_id }})">
                                    <i class="fa-solid fa-download"></i>
                                </span>
                                <span wire:loading wire:target="downloadIndividualStudentGrade({{ $item->user_id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <x-alert type="info" title="Информация" message="Для просмотра списочного состава учебной группы, выберите нужную группу из списка или воспользуйтесь формой поиска" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="getContingent, searchContingentByTicketNumber">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
            </div>
        </div>
    </div>
</div>