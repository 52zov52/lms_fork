<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    @if(!$studentsAcademicLeaves->isEmpty())
    <div class="border rounded border-slate-200 dark:border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 uppercase">
                <tr>
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">ФИО</th>
                    <th scope="col" class="w-64 px-2 py-2 font-semibold">Группа</th>
                    <th scope="col" class="w-64 px-2 py-2 font-semibold">Дата выхода из академ. отпуска</th>
                    <th scope="col" class="w-64 px-2 py-2 font-semibold">Комментарий</th>
                    <th scope="col" class="w-40 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentsAcademicLeaves as $student)
                <tr class="bg-white hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-600 border-b dark:border-gray-700">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2 ">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                    <td class="w-64 px-2 py-2 text-center">{{ $student->group_name }}</td>
                    <td class="w-64 px-2 py-2 text-center">
                        @if(!empty($student->academic_leave_end_date))
                        {{ \Carbon\Carbon::parse($student->academic_leave_end_date)->format('d.m.Y') }}
                        @endif
                    </td>
                    <td class="w-64 px-2 py-2">{{ $student->description }}</td>
                    <td class="w-40 px-2 py-2 text-center">
                        <button wire:click="creatingAndEditingModalFormVisible(true, {{ $student->id }})" class="hover:text-blue-500" data-tippy-content="Редактировать">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Creating And Editing Modal Form -->
    <x-dialog-modal wire:model="isEntryModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Форма создания новой записи') : __('Форма редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <select wire:model="order_id" wire:change="setOrderId" name="order_id" id="order-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Список приказов</option>
                            @foreach($orders as $value)
                            <option value="{{ $value->id }}">{{ $value->order_info }}</option>
                            @endforeach
                        </select>
                        @error('order_id') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="academic-leave-end-date">Дата выхода из академ. отпуска</label>
                        <input type="text" wire:model="academic_leave_end_date" x-mask="99.99.9999" name="academic_leave_end_date" id="academic-leave-end-date" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="День.Месяц.Год">
                        @error('academic_leave_end_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description-id">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col py-2">
                        <label for="is-academic-leave"> <input wire:model="is_academic_leave" type="checkbox" name="is_academic_leave" id="is-academic-leave"> В академическом отпуске</label>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isEntryModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button id="btn-action" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
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
        await @this.callMethod(methodName, param);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
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