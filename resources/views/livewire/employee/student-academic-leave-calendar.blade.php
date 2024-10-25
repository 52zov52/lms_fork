<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @if(!is_null($studentsAcademicLeave) && count($studentsAcademicLeave) != 0)
        <div class="hidden lg:block border rounded border-slate-400/20">
            <table class="w-full text-xs lg:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                        <th scope="col" class="w-48 px-2 py-2 text-left font-semibold">Приказ</th>
                        <th scope="col" class="w-36 px-2 py-2 font-semibold">Группа в которой был студент</th>
                        <th scope="col" class="w-36 px-2 py-2 font-semibold">Дата выхода из академ. отпуска</th>
                        <th scope="col" class="w-20 px-2 py-2 font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsAcademicLeave as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto px-2 py-2">{{ $value->fio }}</td>
                        <td class="w-48 px-2 py-2 text-left">{{ $value->order_info }}</td>
                        <td class="w-36 px-2 py-2 text-center">{{ $value->group_name }}</td>
                        <td class="w-36 px-2 py-2 text-center">{{ $value->academic_leave_end_date }}</td>
                        <td class="w-20 px-2 py-2 text-center">
                            <button wire:click="edit({{ $value->id }}, {{ $value->order_id }})" class="w-9 btn-edit" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="lg:hidden flex flex-col space-y-2">
            @foreach($studentsAcademicLeave as $value)
            <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">ФИО:</span>
                    <span>{{ $value->fio }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Приказ:</span>
                    <span>{{ $value->order_info }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Группа в которой был студент:</span>
                    <span>{{ $value->group_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата выхода из академ. отпуска:</span>
                    <span>{{ $value->academic_leave_end_date }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="flex flex-row space-x-2">
                        <button wire:click="edit({{ $value->id }}, {{ $value->order_id }})" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $studentsAcademicLeave->links() }}
        </div>
        @else
        <x-alert type="info" title="Информация" message="Список студентов, находящихся в академическом отпуске, пуст" />
        @endif
    </div>

    <!-- Modal dialog to edit record -->
    <x-dialog-modal wire:model="isShowModalDialog">
        <form>
            @csrf
            <x-slot name="title">
                Форма редактирования записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="order-id" class="font-semibold">Список приказов<span class="text-red-400">*</span></label>
                        <select wire:model="orderId" wire:change="setOrderId" name="orderId" id="order-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Выберите приказ из списка</option>
                            @foreach($orders as $order)
                            <option value="{{ $order->id }}" wire:key="order-option-{{ $order->id }}" {{ $order->id == $orderId ? 'selected' : '' }}>{{ $order->info }}</option>
                            @endforeach
                        </select>
                        @error('order_id') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="academic-leave-end-date" class="font-semibold">
                            Дата выхода из академ. отпуска<span class="text-red-400">*</span>
                            <span wire:loading wire:target="setOrderId">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </span>
                        </label>
                        <input wire:model="academicLeaveEndDate" type="date" name="academicLeaveEndDate" id="academic-leave-end-date" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('academicLeaveEndDate') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="py-2">
                        <label for="is-academic-leave" class="flex items-center">
                            <input wire:model="isAcademicLeave" type="checkbox" name="isAcademicLeave" id="is-academic-leave" class="mr-1">
                            В академическом отпуске
                        </label>
                    </div>
                    <span class="text-red-500">*Поля обязательные для заполнения</span>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isShowModalDialog')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click="update" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    <span wire:loading.remove wire:target="update">
                        {{ __('Обновить') }}
                    </span>
                    <span wire:loading wire:target="update">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

</div>