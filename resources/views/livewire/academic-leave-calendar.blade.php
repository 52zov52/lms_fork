<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    @if(!is_null($data) && count($data) != 0)
    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th scope="col" class="w-36 px-2 py-2 text-left font-semibold">Группа в которой был студент</th>
                    <th scope="col" class="w-36 px-2 py-2 font-semibold">Дата выхода из академ. отпуска</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->fio }}</td>
                    <td class="w-36 px-2 py-2 text-left">{{ $value->group_name }}</td>
                    <td class="w-36 px-2 py-2 text-center">{{ $value->academic_leave_end_date }}</td>
                    <td class="w-20 px-2 py-2 text-center">
                        <button wire:click="setEntryModalVisible(true, {{ $value->id }}, {{ $value->user_id }}, {{ $value->order_id }})" class="bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Mobile -->
    <div class="lg:hidden flex flex-col space-y-2">
        @foreach($data as $value)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
            <div class="flex flex-col">
                <span class="font-semibold">ФИО:</span>
                <span>{{ $value->fio }}</span>
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
                    <button wire:click="setEntryModalVisible(true, {{ $value->id }}, {{ $value->user_id }}, {{ $value->order_id }})" class="w-auto grow bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white font-bold" title="" data-tippy-content="Редактировать">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-2">
        {{ $data->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Список студентов, находящихся в академическом отпуске, пуст" />
    @endif

    <!-- Entry Modal -->
    <x-dialog-modal wire:model="isEntryModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Добавление записи') : __('Редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="order-id" class="font-semibold">Список приказов<span class="ml-1 text-red-400">*</span></label>
                        <select wire:model="order_id" wire:change="setOrderId" name="order_id" id="order-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Выберите приказ из списка</option>
                            @foreach($orders as $order)
                            <option value="{{ $order->id }}" wire:key="order-option-{{ $order->id }}" {{ $order->id == $order_id ? 'selected' : '' }}>{{ $order->info }}</option>
                            @endforeach
                        </select>
                        @error('order_id') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="academic-leave-end-date" class="font-semibold">Дата выхода из академ. отпуска<span class="ml-1 text-red-400">*</span></label>
                        <input type="text" wire:model="academic_leave_end_date" name="academic_leave_end_date" id="academic-leave-end-date" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="День.Месяц.Год">
                        @error('academic_leave_end_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col py-2">
                        <label for="is-academic-leave"> <input wire:model="is_academic_leave" type="checkbox" name="is_academic_leave" id="is-academic-leave"> В академическом отпуске</label>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isEntryModalVisible')" type="button" class="w-full sm:w-auto bg-white hover:bg-gray-400 border rounded border-gray-400 px-2 py-2 text-xs text-gray-400 hover:text-white font-bold">
                    {{ __('Отменить') }}
                </button>
                <button id="btn-action" type="button" class="ml-2 w-full sm:w-auto bg-white hover:bg-blue-400 border rounded border-blue-400 px-2 py-2 text-xs text-blue-400 hover:text-white font-bold">
                    {{ (!$isEdit) ? __('Добавить') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    const element = document.getElementById('btn-action');
    element.addEventListener('click', function(event) {
        event.preventDefault();
        if (!@this.isEdit) {
            showToast('store');
        } else {
            showToast('update');
        }
    });

    async function showToast(methodName, param = null) {
        await @this.callMethod(methodName, param);

        const toast = document.getElementById("toast");

        if (toast.dataset.notificationType != '') {
            toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

            setTimeout(function() {
                toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
            }, 3500);
        }
    }

    var pickerLessonDate = new Pikaday({
        field: document.getElementById('academic-leave-end-date'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            @this.set('academic_leave_end_date', document.getElementById('cademic-leave-end-date').value);
        }
    });
</script>
@endpush