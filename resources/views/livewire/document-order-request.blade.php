<div class="flex flex-col space-y-2">

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="documentType" name="document_type" id="document-type-id" class="w-full md:w-96 bg-white dark:bg-gray-700 border rounded border-slate-200 dark:border-slate-400/20 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка тип документа (справки)</option>
            <option value="1">Справка о статусе студента</option>
            <option value="2">Справка о статусе студента (для транспортных карт) - только для студентов колледжа</option>
        </select>
        <button wire:click="confirmOrderDocumentModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-paper-plane"></i>
            Отправить заявку
        </button>
    </div>

    @if(!$documentOrderRequest->isEmpty())
    <div class="border rounded border-slate-200 dark:border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 uppercase">
                <tr>
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Тип документа</th>
                    <th scope="col" class="w-48 px-2 py-2 font-semibold">Статус заявки</th>
                    <th scope="col" class="w-48 px-2 py-2 font-semibold">Дата и время создания заявки</th>
                    <th scope="col" class="w-48 px-2 py-2 font-semibold">Дата и время получения документа</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documentOrderRequest as $item)
                <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 border-b dark:border-gray-700">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">
                        @if($item->document_type == 1)
                            <span>Справка о статусе студента</span>
                        @else
                            <span>Справка о статусе студента (для транспортных карт)</span>
                        @endif
                    </td>
                    <td class="w-48 px-2 py-2 text-center">
                        @if($item->status == 1)
                            <div class="flex flex-col bg-orange-500 rounded py-1 text-xs text-white font-bold">
                                <span>В обработке</span>
                            </div>
                        @elseif($item->status == 2)
                            <div class="flex flex-col bg-blue-500 rounded py-1 text-xs text-white font-bold">
                                <span>Готово к выдаче</span>
                                <span>{{ \Carbon\Carbon::parse($item->confirmed_date)->addDay()->format('d.m.Y') }} после 13:00</span>
                            </div>
                        @elseif($item->status == 3)
                            <div class="flex flex-col bg-red-500 rounded py-1 text-xs text-white font-bold">
                                <span>Отклонена</span>
                            </div>
                        @else
                            <div class="flex flex-col bg-green-500 rounded py-1 text-xs text-white font-bold">
                                <span>Выполнена</span>
                            </div>
                        @endif
                    </td>
                    <td class="w-48 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($item->request_date)->format('d.m.Y H:i:s') }}</td>
                    <td class="w-48 px-2 py-2 text-center">
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
    @else
        <x-alert type="info" title="Информация!" message="Информация о запросах на выдачу документов (справок) отсутсвует." />
    @endif

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