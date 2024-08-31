<div class="flex flex-col space-y-2">
    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="selectedDocumentType" wire:change="setDocumentType" name="documentType" id="document-type" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка тип справки</option>
            @foreach($listDocumentTypes as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>

    @if(!is_null($studentDocumentIssuances) && count($studentDocumentIssuances) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th scope="col" class="W-32 px-2 py-2 text-left font-semibold">Группа</th>
                    <th scope="col" class="w-52 px-2 py-2 text-left font-semibold">Тип справки</th>
                    <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Учебный год</th>
                    <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                    <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Дата создания заявки</th>
                    <th scope="col" class="w-28 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentDocumentIssuances as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2 text-left">{{ $value->fio }}</td>
                    <td class="w-32 px-2 py-2 text-left">{{ $value->group_name }}</td>
                    <td class="w-52 px-2 py-2 text-left">
                        @if($value->document_type == 1)
                        {{ __('Справка о статусе студента') }}
                        @elseif($value->document_type == 2)
                        {{ __('Справка о статусе студента (для транспортных карт)') }}
                        @else
                        {{ __('Справка-вызов') }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }}</td>
                    <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                    <td class="w-40 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->created_at)->format('d.m.Y') }}</td>
                    <td class="w-28 px-2 py-2 text-center">
                        <div class="flex flex-row items-center justify-center space-x-1">
                            <button wire:click="edit({{ $value->student_document_request_id }}, {{ $value->requester_id }}, {{ $value->document_type }})" class="w-9 bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-green-400 font-bold" title="" data-tippy-content="Подтверждение">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button wire:click="print({{ $value->student_document_request_id }})" class="w-9 bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-blue-400 font-bold" title="" data-tippy-content="Печать справки">
                                <i class="fa-solid fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-2">
        {{ $studentDocumentIssuances->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Список заявок на выдачу справок пуст" />
    @endif

    <x-dialog-modal wire:model="showModal">
        <form>
            @csrf
            <x-slot name="title">
                Форма подтверждения заявки на справку
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="document-number" class="font-semibold">Номер справки<span class="ml-1 text-red-400">*</span></label>
                        <input wire:model="documentNumber" type="text" id="document-number" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('documentNumber') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="last-name" class="font-semibold">Фамилия<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="lastName" type="text" id="last-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="first-name" class="font-semibold">Имя<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="firstName" type="text" id="first-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="middle-name" class="font-semibold">Отчество (при наличии)</label>
                            <input wire:model="middleName" type="text" id="middle-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="last-name-dative" class="font-semibold">Фамилия<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="lastNameDative" type="text" id="last-name-dative" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="first-name-dative" class="font-semibold">Имя<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="firstNameDative" type="text" id="first-name-dative" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="middle-name-dative" class="font-semibold">Отчество (при наличии)</label>
                            <input wire:model="middleNameDative" type="text" id="middle-name-dative" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="course" class="font-semibold">Курс<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="course" type="text" id="course" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('course') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="teaching-form-name" class="font-semibold">Форма обучения<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="teachingFormName" type="text" id="teaching-form-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('teachingFormName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="specialty-code" class="font-semibold">Шифр<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="specialtyCode" type="text" id="specialty-code" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('specialtyCode') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="speciality-name" class="font-semibold">Специальность<span class="ml-1 text-red-400">*</span></label>
                        <input wire:model="specialityName" type="text" id="speciality-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('specialityName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="order" class="font-semibold">Приказ о зачислении<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="order" type="text" id="order" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('order') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="training-period" class="font-semibold">Срок обучения<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="trainingPeriod" type="text" id="training-period" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('trainingPeriod') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="check-in-date" class="font-semibold">Период заезда с<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="checkInDate" type="text" id="check-in-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('checkInDate') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="check-out-date" class="font-semibold">Период заезда по<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="checkOutDate" type="text" id="check-out-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('checkOutDate') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="check-in-duration" class="font-semibold">Продолжительность<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="checkInDuration" type="text" id="check-in-duration" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('checkInDuration') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="employment-place" class="font-semibold">Место работы<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="employmentPlace" name="employmentPlace" id="employment-place" cols="5" rows="2" placeholder="Укажите текущее место работы студента" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('employmentPlace') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col py-2">
                        <label for="is-budget"> <input wire:model="isBudget" type="checkbox" name="isBudget" id="is-budget"> Обучается на бюджете</label>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('showModal')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button id="modal-action-btn" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Подтвердить заявку') }}
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
        showToast('update');
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

    var pickerСheckInDate = new Pikaday({
        field: document.getElementById('check-in-date'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            @this.set('checkInDate', document.getElementById('check-in-date').value);
        }
    });
    var pickerСheckOutDate = new Pikaday({
        field: document.getElementById('check-out-date'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            @this.set('checkOutDate', document.getElementById('check-out-date').value);
        }
    });
</script>
@endpush