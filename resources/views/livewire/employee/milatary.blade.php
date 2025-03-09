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
        </div>

        <div class="flex flex-row space-x-2">
            <input wire:model="docNumber" type="text" name="docNumber" id="doc-number" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 px-2 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-600 dark:placeholder-white" placeholder="Введите номер документа для приложения № 4">
        </div>

        <div wire:loading.remove wire:target="getContingent, searchContingentByTicketNumber">
            @if(!is_null($contingent) && count($contingent) != 0)
            <div class="border rounded border-slate-400/20">
                <table class="w-full text-xs md:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                            <th scope="col" class="w-64 px-2 py-2 font-semibold">Группа</th>
                            <th scope="col" class="w-64 px-2 py-2 font-semibold">№ зачетки</th>
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
                            <td class="w-24 px-2 py-2 text-center">
                                <button wire:click="milatary({{ $item->user_id }})" class="w-9 btn-download" title="" data-tippy-content="Скачать форму № 10">
                                    <span wire:loading.remove wire:target="milatary({{ $item->user_id }})">
                                        <i class="fa-solid fa-download"></i>
                                    </span>
                                    <span wire:loading wire:target="milatary({{ $item->user_id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button wire:click="form4({{ $item->user_id }})" class="w-9 btn-download" title="" data-tippy-content="Скачать форму № 4">
                                    <span wire:loading.remove wire:target="form4({{ $item->user_id }})">
                                        <i class="fa-solid fa-download"></i>
                                    </span>
                                    <span wire:loading wire:target="form4({{ $item->user_id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <x-alert type="info" title="Информация" message="Для просмотра списочного состава учебной группы, выберите нужную группу из списка" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="getContingent">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
            </div>
        </div>
    </div>
</div>