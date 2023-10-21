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
        <select wire:model="documentType" name="document_type" id="document-type-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка документ</option>
            <option value="1">Справка об обучении</option>
            <option value="2">Справка об обучении (для транспортных карт)</option>
        </select>
        <button wire:click="orderDocument" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">Заказать документ</button>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$documentOrderRequest->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Тип документа</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Статус запроса</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Дата и время создания заявки</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Дата и время получения документа</td>
                    </tr>
                    @foreach($documentOrderRequest as $item)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">
                            @if($item->document_type == 1)
                            <span>Справка об обучении</span>
                            @else
                            <span>Справка об обучении (для транспортных карт)</span>
                            @endif
                        </td>
                        <td class="w-auto border px-2 py-2 text-center">
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Mobile -->
        <div class="block md:hidden">
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Тип документа:</span>
                    @if($item->document_type == 1)
                    <span>Справка об обучении</span>
                    @else
                    <span>Справка об обучении (для транспортных карт)</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Статус запроса:</span>
                    @if($item->status == 1)
                    <span class="w-24 bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В обработке</span>
                    @elseif($item->status == 2)
                    <span class="w-28 bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">Готов к выдаче</span>
                    @elseif($item->status == 3)
                    <span class="w-24 bg-red-500 rounded px-2 py-1 text-xs text-white font-bold">Отклонен</span>
                    @else
                    <span class="w-24 bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Получен</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата и время создания заявки</span>
                    <span>{{ $item->request_date }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата и время получения документа</span>
                    @if($item->issue_date != null)
                    <span>{{ \Carbon\Carbon::parse($item->issue_date)->format('d.m.Y H:i:s') }}</span>
                    @else
                    <span>-</span>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация о запросах на выдачу справок отсутсвует.
            </div>
        </div>
        @endif
    </div>
</div>