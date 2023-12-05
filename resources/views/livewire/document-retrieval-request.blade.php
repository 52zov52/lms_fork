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

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$documentOrderRequest->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Группа</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Тип документа</td>
                        <td class="w-32 border px-2 py-2 text-center font-semibold">Статус запроса</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Дата и время создания заявки</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Дата и время получения документа</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Действия</td>
                    </tr>
                    @foreach($documentOrderRequest as $item)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                        <td class="w-auto border px-2 py-2">{{ $item->group_name }}</td>
                        <td class="w-auto border px-2 py-2">
                            @if($item->document_type == 1)
                            <span>Справка о статусе студента</span>
                            @else
                            <span>Справка о статусе студента (для транспортных карт)</span>
                            @endif
                        </td>
                        <td class="w-32 border px-2 py-2 text-center">
                            @if($item->status == 1)
                            <span class="bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В обработке</span>
                            @elseif($item->status == 2)
                            <span class="bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">Готов к выдаче</span>
                            @elseif($item->status == 3)
                            <span class="bg-red-500 rounded px-2 py-1 text-xs text-white font-bold">Отклонен</span>
                            @else
                            <span class="bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Получен</span>
                            @endif
                        </td>
                        <td class="w-auto border px-2 py-2 text-center">{{ \Carbon\Carbon::parse($item->request_date)->format('d.m.Y H:i:s') }}</td>
                        <td class="w-auto border px-2 py-2 text-center">
                            @if($item->issue_date != null)
                            <span>{{ \Carbon\Carbon::parse($item->issue_date)->format('d.m.Y H:i:s') }}</span>
                            @else
                            <span>-</span>
                            @endif
                        </td>
                        <td class="w-auto border px-2 py-2 text-center">
                            <button wire:click="documentOrderRequestModalVisible({{ $item->id }}, {{ $item->requester_id }})" title="Подтвердить данные"><i class="fa-solid fa-square-check"></i></button>
                            <button wire:click="printDocument({{ $item->id }})" title="Распечатать документ"><i class="fa-solid fa-print"></i></button>
                            <button wire:click="confirmRetrievalRequest({{ $item->id }})" title="Подтвердить выдачу документа"><i class="fa-solid fa-square-arrow-up-right"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
                    <span class="font-semibold">Статус запроса:
                        @if($item->status == 1)
                        <span class="w-24 bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В обработке</span>
                        @elseif($item->status == 2)
                        <span class="w-28 bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">Готов к выдаче</span>
                        @elseif($item->status == 3)
                        <span class="w-24 bg-red-500 rounded px-2 py-1 text-xs text-white font-bold">Отклонен</span>
                        @else
                        <span class="w-24 bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Получен</span>
                        @endif
                    </span>
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
                        <button wire:click="documentOrderRequestModalVisible({{ $item->id }}, {{ $item->requester_id }})" title="Подтвердить данные" class="w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-square-check"></i>
                        </button>
                        <button wire:click="printDocument({{ $item->id }})" title="Распечатать документ" class="w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-print"></i>
                        </button>
                        <button wire:click="confirmRetrievalRequest({{ $item->id }})" title="Подтвердить выдачу документа" class="w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-square-arrow-up-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
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
                            <label for="last-name-id">Фамилия:</label>
                            <input wire:model="lastName" type="text" name="last_name" id="last-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="first-name-id">Имя:</label>
                            <input wire:model="firstName" type="text" name="first_name" id="first-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="middle-name-id">Отчество:</label>
                            <input wire:model="middleName" type="text" name="middle_name" id="middle-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="course-id">Курс:</label>
                            <input wire:model="course" type="text" name="course" id="course-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('course')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="teaching-form-name-id">Форма обучения:</label>
                            <input wire:model="teachingFormName" type="text" name="teaching_form_name" id="teaching-form-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('teachingFormName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="specialty-сode-id">Шифр:</label>
                            <input wire:model="specialtyCode" type="text" name="specialty_сode" id="specialty-сode-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('specialtyCode')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="speciality-name-id">Специальность:</label>
                            <input wire:model="specialityName" type="text" name="speciality_name" id="speciality-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('specialityName')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="orders-id">Приказы:</label>
                        <textarea wire:model="orders" name="orders" id="orders-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ $orders }}</textarea>
                        @error('orders')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="training-period-id">Срок обучения:</label>
                        <input wire:model="trainingPeriod" type="text" name="training_period" id="training-period-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('trainingPeriod')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="flex flex-col py-2">
                        <label for="is-budget-id"> <input wire:model="isBudget" type="checkbox" name="isBudget" id="is-budget-id"> Обучается на бюджете</label>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isDocumentOrderRequestModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="checkRetrievalRequest" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Подтвердить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>