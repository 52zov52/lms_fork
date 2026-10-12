<div class="flex flex-col space-y-2">
    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="selectedDocumentType" wire:change="setDocumentType" name="documentType" id="document-type" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка тип справки</option>
            @foreach($listDocumentTypes as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        <div class="flex flex-col">
            <label class="flex items-center space-x-1 text-xs">
                <span>Кол-во экз.</span>
                <input wire:model="quantity" type="number" min="1" max="10" name="quantity" id="quantity" class="w-16 bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </label>
            @error('quantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
        <button type="button" onclick="showToast('sendRequest')" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-left text-green-400 hover:text-white font-bold">
            <i class="fa-solid fa-paper-plane"></i>
            Отправить заявку
        </button>
    </div>

    <div class="flex flex-row space-x-2">
        <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В работе</span>
        <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Выполнена</span>
        <span class="bg-red-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Отклонена</span>
        <span class="text-xs md:text-sm text-slate-500 self-center">Наведите на <i class="fa-solid fa-circle-info text-red-400"></i> у отклонённой заявки, чтобы увидеть причину</span>
    </div>

    @if(!is_null($studentDocumentRequests) && count($studentDocumentRequests) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Тип справки</th>
                    <th scope="col" class="w-16 px-2 py-2 text-center font-semibold">Статус заявки</th>
                    <th scope="col" class="w-16 px-2 py-2 text-center font-semibold">Кол-во</th>
                    <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Учебный год</th>
                    <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                    <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Дата создания заявки</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentDocumentRequests as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2 text-left">
                        @if($value->document_type == 1)
                        {{ __('Справка о статусе студента') }}
                        @elseif($value->document_type == 2)
                        {{ __('Справка о статусе студента (для транспортных карт)') }}
                        @elseif($value->document_type == 3)
                        {{ __('Справка-вызов') }}
                        @else
                        {{ __('Характеристика') }}
                        @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value->status == 1)
                        <span class="text-orange-400 font-bold cursor-pointer" data-tippy-content="В работе">Р</span>
                        @elseif($value->status == 2)
                        <span class="text-green-400 font-bold cursor-pointer" data-tippy-content="Выполнена">В</span>
                        @else
                        @if(!empty($value->reject_reason))
                        <span class="inline-flex items-center space-x-1 text-red-400 font-bold cursor-help border-b border-dotted border-red-400" data-tippy-content="Отклонена. Причина: {{ e($value->reject_reason) }}">
                            <span>О</span>
                            <i class="fa-solid fa-circle-info text-xs"></i>
                        </span>
                        @else
                        <span class="text-red-400 font-bold cursor-pointer" data-tippy-content="Отклонена">О</span>
                        @endif
                        @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->quantity }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }}</td>
                    <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                    <td class="w-40 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->created_at)->format('d.m.Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-2">
        {{ $studentDocumentRequests->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Список заявок на выдачу справок пуст" />
    @endif

    <x-dialog-modal wire:model="showModal">
        <form>
            @csrf
            <x-slot name="title">
                Форма заявки на справку-вызов
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="employment-place" class="font-semibold">Место работы<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="employmentPlace" name="employmentPlace" id="employment-place" cols="5" rows="2" placeholder="Укажите ваше текущее место работы" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required></textarea>
                        @error('employmentPlace') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('showModal')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button id="modal-action-btn" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Отправить заявку') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    const element = document.getElementById('modal-action-btn');
    element.addEventListener('click', function(event) {
        event.preventDefault();
        showToast('store');
    });

    async function showToast(methodName) {
        await @this.callMethod(methodName);;

        const toast = document.getElementById("toast");

        if (toast.dataset.notificationType != '') {
            toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

            setTimeout(function() {
                toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
            }, 3500);
        }
    }
</script>
@endpush