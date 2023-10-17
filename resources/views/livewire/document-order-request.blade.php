<div class="flex flex-col">

    @if(session()->has('message'))
    {{ session('message') }}
    @endif
    
    <div class="flex flex-row space-x-2">
        <select wire:model="documentType" name="document_type" id="document-type-id">
            <option value="0">Выберите из списка документ</option>
            <option value="1">Справк об обучении</option>
            <option value="2">Справк об обучении (для транспортных карт)</option>
        </select>
        <button wire:click="orderDocument">заказать документ</button>
    </div>

    <div>
        @if(!$documentOrderRequest->isEmpty())
        <table>
            <tbody>
                <tr>
                    <td>№ пп</td>
                    <td>Тип документа</td>
                    <td>Статус запроса</td>
                    <td>Дата и время создания заявки</td>
                    <td>Дата и время получения документа</td>
                </tr>
                @foreach($documentOrderRequest as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($item->document_type == 1)
                        <span>Справка об обучении</span>
                        @else
                        <span>Справка об обучении (для транспортных карт)</span>
                        @endif
                    </td>
                    <td>
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
                    <td>{{ $item->request_date }}</td>
                    <td>
                        @if($item->issue_date != null)
                        {{ \Carbon\Carbon::parse($item->issue_date)->format('d.m.Y H:i:s') }}
                        @else
                        <span>-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div>
            Данные отсутсвуют
        </div>
        @endif
    </div>
</div>
