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

    @if(session()->has('message-error') && $isAlertVisible)
    <div class="flex items-center justify-between bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-xmark fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                {{ session('message-error') }}
            </div>
        </div>
        <button wire:click="alertVisible"><i class="fa-solid fa-xmark fal-lg"></i></button>
    </div>
    @endif

    <div class="bg-white rounded shadow-xl p-2">
        @if(!$documentOrderRequest->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-xs">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-10 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                        <td class="w-14 border px-2 py-2 font-semibold">Группа</td>
                        <td class="w-48 border px-2 py-2 font-semibold">Тип документа</td>
                        <td class="w-48 border px-2 py-2 text-center font-semibold">Статус запроса</td>
                        <td class="w-40 border px-2 py-2 text-center font-semibold">Дата и время создания заявки</td>
                        <td class="w-40 border px-2 py-2 text-center font-semibold">Дата и время получения документа</td>
                        <td class="w-28 border px-2 py-2 text-center font-semibold">Действия</td>
                    </tr>
                    @foreach($documentOrderRequest as $item)
                    <tr>
                        <td class="w-10 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                        <td class="w-14 border px-2 py-2">{{ $item->group_name }}</td>
                        <td class="w-48 border px-2 py-2">
                            @if($item->document_type == 1)
                                <span>Справка о статусе студента</span>
                            @else
                                <span>Справка о статусе студента (для транспортных карт)</span>
                            @endif
                        </td>
                        <td class="w-48 border px-2 py-2 text-center">
                            @if($item->status == 1)
                                <div class="flex flex-col bg-orange-500 rounded py-1 text-xs text-white font-bold">
                                    <span>В обработке</span>
                                </div>
                            @elseif($item->status == 2)
                                <div class="flex flex-col bg-blue-500 rounded py-1 text-xs text-white font-bold">
                                    <span>Готов к выдаче</span>
                                    <span>{{ \Carbon\Carbon::parse($item->confirmed_date)->addDay()->format('d.m.Y') }} после 13:00</span>
                                </div>
                            @elseif($item->status == 3)
                                <div class="flex flex-col bg-red-500 rounded py-1 text-xs text-white font-bold">
                                    <span>Отказано</span>
                                </div>
                            @else
                                <div class="flex flex-col bg-green-500 rounded py-1 text-xs text-white font-bold">
                                    <span>Получен</span>
                                </div>
                            @endif
                        </td>
                        <td class="w-40 border px-2 py-2 text-center">{{ \Carbon\Carbon::parse($item->request_date)->format('d.m.Y H:i:s') }}</td>
                        <td class="w-40 border px-2 py-2 text-center">
                            @if($item->issue_date != null)
                                <span>{{ \Carbon\Carbon::parse($item->issue_date)->format('d.m.Y H:i:s') }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td class="w-28 border py-2 text-center">
                            <button wire:click="edit({{ $item->id }}, {{ $item->requester_id }})" class="bg-orange-500 hover:bg-orange-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Подтвердить данные в заявке">
                                <i class="fa-solid fa-file-circle-check"></i>
                            </button>
                            <button wire:click="printDocument({{ $item->id }})" class="bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Распечатать документ (справку)">
                                <i class="fa-solid fa-print"></i>
                            </button>
                            <button wire:click="confirmDocumentIssuanceModalVisible({{ $item->id }})" class="bg-green-500 hover:bg-green-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Подтвердить выдачу документа (справки)">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">
                {{ $documentOrderRequest->links() }}
            </div>
        </div>
        <!-- Mobile -->
        <div class="block md:hidden flex flex-col space-y-2">
            @foreach($documentOrderRequest as $item)
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">ФИО:</span>
                    <span>{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Группа:</span>
                    <span>{{ $item->group_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Тип документа:</span>
                    @if($item->document_type == 1)
                        <span>Справка о статусе студента</span>
                    @else
                        <span>Справка о статусе студента (для транспортных карт)</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Статус заявки:</span>
                    @if($item->status == 1)
                        <span class="w-24 bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В обработке</span>
                    @elseif($item->status == 2)
                        <span class="w-60 bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">Готов к выдаче {{ \Carbon\Carbon::parse($item->confirmed_date)->addDay()->format('d.m.Y') }} после 13:00</span>
                    @elseif($item->status == 3)
                        <span class="w-24 bg-red-500 rounded px-2 py-1 text-xs text-white font-bold">Отказано</span>
                    @else
                        <span class="w-20 bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Получен</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата и время создания заявки:</span>
                    <span>{{ \Carbon\Carbon::parse($item->request_date)->format('d.m.Y H:i:s') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата и время получения документа:</span>
                    @if($item->issue_date != null)
                        <span>{{ \Carbon\Carbon::parse($item->issue_date)->format('d.m.Y H:i:s') }}</span>
                    @else
                        <span>-</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="grid grid-cols-3 gap-2">
                        <button wire:click="edit({{ $item->id }}, {{ $item->requester_id }})" class="w-auto bg-orange-500 hover:bg-orange-600 rounded px-2 py-1 text-white" title="Подтвердить данные в заявке">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </button>
                        <button wire:click="printDocument({{ $item->id }})"  class="w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white" title="Распечатать документ (справку)">
                            <i class="fa-solid fa-print"></i>
                        </button>
                        <button wire:click="confirmDocumentIssuanceModalVisible({{ $item->id }})" class="w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-1 text-white" title="Подтвердить выдачу документа (справки)">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="mt-2">
                {{ $documentOrderRequest->links() }}
            </div>
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует. На текущий момент нет активных заявок на получение документов (справок)
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog document order request -->
    <x-dialog-modal wire:model="isDocumentOrderRequestModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма просмотра заявки на документ (справку)
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label for="last-name-id" class="font-semibold">Фамилия<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="lastName" type="text" name="last_name" id="last-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="first-name-id" class="font-semibold">Имя<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="firstName" type="text" name="first_name" id="first-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="middle-name-id" class="font-semibold">Отчество<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="middleName" type="text" name="middle_name" id="middle-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="course-id" class="font-semibold">Курс<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="course" type="text" name="course" id="course-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('course')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="teaching-form-name-id" class="font-semibold">Форма обучения<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="teachingFormName" type="text" name="teaching_form_name" id="teaching-form-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('teachingFormName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="specialty-сode-id" class="font-semibold">Шифр<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="specialtyCode" type="text" name="specialty_сode" id="specialty-сode-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('specialtyCode')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="speciality-name-id" class="font-semibold">Специальность<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="specialityName" type="text" name="speciality_name" id="speciality-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('specialityName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="orders-id"class="font-semibold">Приказы<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="orders" name="orders" id="orders-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ $orders }}</textarea>
                        @error('orders')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="training-period-id" class="font-semibold">Срок обучения<span class="ml-1 text-red-500">*</span></label>
                        <input wire:model="trainingPeriod" type="text" name="training_period" id="training-period-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('trainingPeriod')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="flex flex-col py-2">
                        <label for="is-budget-id"> <input wire:model="isBudget" type="checkbox" name="isBudget" id="is-budget-id"> Обучается на бюджете</label>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isDocumentOrderRequestModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="store" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Подтвердить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog to confirm document issuance -->
    <x-dialog-modal wire:model="isConfirmDocumentIssuanceModalVisible" maxWidth="md">
        <x-slot name="title">
            Подтверждение выдачи документа (справки)
        </x-slot>

        <x-slot name="content">
            <div class="w-full flex flex-col">
                <h2 class="text-lg">Вы действительно хотите подтвердить выдачу документа (справки)?</h2>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" wire:click="$toggle('isConfirmDocumentIssuanceModalVisible')" class="w-full sm:w-12 bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                Нет
            </button>
            <button type="button" wire:click="confirmDocumentIssuance" class="ml-2 w-full sm:w-12 bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                Да
            </button>
        </x-slot>
    </x-dialog-modal>
</div>