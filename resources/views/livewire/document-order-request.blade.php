<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="documentType" name="document_type" id="document-type-id" class="w-full md:w-96 bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка тип документа (справки)</option>
            <option value="1">Справка о статусе студента</option>
            <option value="2">Справка о статусе студента (для транспортных карт) - только для студентов колледжа</option>
        </select>
        <button onclick="showToast('store')" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-paper-plane"></i>
            Отправить заявку
        </button>
    </div>

    @if(!$documentOrderRequest->isEmpty())
    <!-- Desktop -->
    <div class="hidden md:block border rounded border-slate-200 dark:border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
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
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-600 border-b dark:border-slate-700">
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
                        <div class="bg-orange-400 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 py-1 text-xs text-white dark:text-orange-400 font-bold">
                            <span>В обработке</span>
                        </div>
                        @elseif($item->status == 2)
                        <div class="flex flex-col bg-sky-400 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 py-1 text-xs text-white dark:text-sky-400 font-bold">
                            <span>Готово к выдаче</span>
                            <span>{{ \Carbon\Carbon::parse($item->confirmed_date)->addDay()->format('d.m.Y') }} после 13:00</span>
                        </div>
                        @elseif($item->status == 3)
                        <div class="bg-red-400 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 py-1 text-xs text-white dark:text-red-400 font-bold">
                            <span>Отклонена</span>
                        </div>
                        @else
                        <div class="bg-green-400 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 py-1 text-xs text-white dark:text-green-400 font-bold">
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
    <!-- Mobile -->
    <div class="md:hidden flex flex-col space-y-2">
        @foreach($documentOrderRequest as $item)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
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
                <span class="text-orange-400 font-semibold">В обработке</span>
                @elseif($item->status == 2)
                <span class="text-sky-400 font-semibold">Готово к выдаче {{ \Carbon\Carbon::parse($item->confirmed_date)->addDay()->format('d.m.Y') }} после 13:00</span>
                @elseif($item->status == 3)
                <span class="text-red-400 font-semibold">Отклонена</span>
                @else
                <span class="text-green-400 font-semibold">Выполнена</span>
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
    </div>
    @else
        <x-alert type="info" title="Информация" message="Информация о запросах на выдачу документов (справок) отсутсвует." />
    @endif
</div>

@push('scripts')
<script>
    async function showToast(methodName) {
        await @this.callMethod(methodName);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }
</script>
@endpush