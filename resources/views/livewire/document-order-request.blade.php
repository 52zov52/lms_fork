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

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="documentType" name="document_type" id="document-type-id" class="w-full md:w-96 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка тип документа (справки)</option>
            <option value="1">Справка о статусе студента</option>
            <option value="2">Справка о статусе студента (для транспортных карт) - только для студентов колледжа</option>
        </select>
        <button wire:click="confirmOrderDocumentModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-paper-plane"></i>
            Заказать документ
        </button>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$documentOrderRequest->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-sm">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-14 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Тип документа</td>
                        <td class="w-48 border px-2 py-2 text-center font-semibold">Статус заявки</td>
                        <td class="w-48 border px-2 py-2 text-center font-semibold">Дата и время создания заявки</td>
                        <td class="w-48 border px-2 py-2 text-center font-semibold">Дата и время получения документа</td>
                    </tr>
                    @foreach($documentOrderRequest as $item)
                    <tr>
                        <td class="w-14 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">
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
                        <td class="w-48 border px-2 py-2 text-center">{{ \Carbon\Carbon::parse($item->request_date)->format('d.m.Y H:i:s') }}</td>
                        <td class="w-48 border px-2 py-2 text-center">
                            @if($item->issue_date != null)
                                <span>{{ \Carbon\Carbon::parse($item->issue_date)->format('d.m.Y H:i:s') }}</span>
                            @else
                                <span>-</span>
                            @endif
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
                Информация о запросах на выдачу документов (справок) отсутсвует.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog to confirm order document -->
    <x-dialog-modal wire:model="isConfirmOrderDocumentModalVisible" maxWidth="md">
        <x-slot name="title">
            Подтверждение создания заявки на выдачу документа (справки)
        </x-slot>

        <x-slot name="content">
            <div class="w-full flex flex-col">
                <h2 class="text-lg">Вы действительно хотите создать заявку на выдачу документа (справки)?</h2>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" wire:click="$toggle('isConfirmOrderDocumentModalVisible')" class="w-full sm:w-12 bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                Нет
            </button>
            <button type="button" wire:click="store" class="ml-2 w-full sm:w-12 bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                Да
            </button>
        </x-slot>
    </x-dialog-modal>
</div>