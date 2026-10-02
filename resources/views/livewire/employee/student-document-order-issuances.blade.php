<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <select wire:model="selectedDocumentType" wire:change="setDocumentType" name="selectedDocumentType" id="selected-document-type" class="w-full md:w-auto form-select">
                <option value="0">Выберите из списка тип справки</option>
                @foreach($listOfDocumentTypes as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <select wire:model="selectedStatus" wire:change="setStatus" name="selectedStatus" id="selected-status" class="w-full md:w-auto form-select">
                <option value="0">Выберите из списка статус заявки</option>
                @foreach($listOfStatuses as $keyStatus => $valueStatus)
                <option value="{{ $keyStatus }}">{{ $valueStatus }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-row space-x-2">
            <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В работе</span>
            <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Выполнена</span>
            <span class="bg-red-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Отклонена</span>
        </div>

        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <p class="text-xs md:text-sm text-justify font-semibold text-red-400">*Печать справки возможно только после её подтверждения!</p>
        </div>

        <div wire:loading.remove wire:target="setDocumentType, setStatus">
            @if(!is_null($studentDocumentOrderIssuances) && count($studentDocumentOrderIssuances) != 0)
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                            <th scope="col" class="W-32 px-2 py-2 text-left font-semibold">Группа</th>
                            <th scope="col" class="w-52 px-2 py-2 text-left font-semibold">Тип справки</th>
                            <th scope="col" class="w-16 px-2 py-2 text-center font-semibold">Статус заявки</th>
                            <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Учебный год</th>
                            <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                            <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Дата создания заявки</th>
                            <th scope="col" class="w-28 px-2 py-2 text-center font-semibold">Действия*</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentDocumentOrderIssuances as $value)
                        <tr wire:key="{{ $value->id }}" class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto px-2 py-2 text-left">{{ $value->fio }}</td>
                            <td class="w-32 px-2 py-2 text-left">{{ $value->group_name }}</td>
                            <td class="w-52 px-2 py-2 text-left">
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
                                <span class="text-red-400 font-bold cursor-pointer" data-tippy-content="Отклонена">О</span>
                                @endif
                            </td>
                            <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }}</td>
                            <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                            <td class="w-40 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->created_at)->format('d.m.Y') }}</td>
                            <td class="w-28 px-2 py-2 text-center">
                                <div class="flex flex-row items-center justify-center space-x-1">
                                    @if(is_null($value->confirmiter_id))
                                    <button wire:click="edit({{ $value->student_document_order_request_id }}, {{ $value->requester_id }}, {{ $value->document_type }})" class="w-9 btn-check" data-tippy-content="Подтверждение заявки">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    @else
                                    <button wire:click="downloadStudentDocument({{ $value->student_document_order_request_id }})" class="w-9 btn-download" data-tippy-content="Скачать заказанный документ" @if(is_null($value->confirmiter_id)) disabled @endif>
                                        <span wire:loading.remove wire:target="downloadStudentDocument({{ $value->student_document_order_request_id }})">
                                            <i class="fa-solid fa-download"></i>
                                        </span>
                                        <span wire:loading wire:target="downloadStudentDocument({{ $value->student_document_order_request_id }})">
                                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                        </span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($studentDocumentOrderIssuances as $value)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">ФИО:</span>
                        <span>{{ $value->fio }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Группа:</span>
                        <span>{{ $value->group_name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Тип справки:</span>
                        @if($value->document_type == 1)
                        <span>{{ __('Справка о статусе студента') }}</span>
                        @elseif($value->document_type == 2)
                        <span>{{ __('Справка о статусе студента (для транспортных карт)') }}</span>
                        @elseif($value->document_type == 3)
                        <span>{{ __('Справка-вызов') }}</span>
                        @else
                        <span>{{ __('Характеристика') }}</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Статус заявки:</span>
                        @if($value->status == 1)
                        <span class="text-orange-400 font-semibold">В работе</span>
                        @elseif($value->status == 2)
                        <span class="text-green-400 font-semibold">Выполнена</span>
                        @else
                        <span class="text-red-400 font-semibold">Отклонена</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Учебный год:</span>
                        <span>{{ $value->academic_year }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Полугодие:</span>
                        <span>{{ $value->half_year }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Дата создания заявки:</span>
                        <span>{{ \Carbon\Carbon::parse($value->created_at)->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Действия:</span>
                        <div class="flex flex-row space-x-2">
                            @if(is_null($value->confirmiter_id))
                            <button wire:click="edit({{ $value->student_document_order_request_id }}, {{ $value->requester_id }}, {{ $value->document_type }})" class="w-auto grow btn-check" data-tippy-content="Подтверждение заявки">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            @else
                            <button wire:click="downloadStudentDocument({{ $value->student_document_order_request_id }})" class="w-auto grow btn-download" data-tippy-content="Скачать заказанный документ" @if(is_null($value->confirmiter_id)) disabled @endif>
                                <span wire:loading.remove wire:target="downloadStudentDocument({{ $value->student_document_order_request_id }})">
                                    <i class="fa-solid fa-download"></i>
                                </span>
                                <span wire:loading wire:target="downloadStudentDocument({{ $value->student_document_order_request_id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $studentDocumentOrderIssuances->links() }}
            </div>
            @else
            <x-alert type="info" title="Информация" message="Список заявок на выдачу документов пуст" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="setDocumentType, setStatus">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
            </div>
        </div>
    </div>

    <!-- Modal dialog to confirm student document order -->
    <x-dialog-modal wire:model="isShowModalDialog" maxWidth="4xl">
        <form>
            @csrf
            <x-slot name="title">
                Форма подтверждения заявки на документ
            </x-slot>

            <x-slot name="content">
                @if($isShowModalDialog)
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="document-number" class="font-semibold">Номер справки (присваивается автоматически)</label>
                        <input wire:model="documentNumber" type="text" name="documentNumber" id="document-number" readonly class="w-full border border-gray-300 bg-gray-100 rounded px-2 text-sm md:text-base">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="last-name" class="font-semibold">Фамилия (И. п.)<span class="text-red-400">*</span></label>
                            <input wire:model="lastName" type="text" name="lastName" id="last-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="first-name" class="font-semibold">Имя (И. п.)<span class="text-red-400">*</span></label>
                            <input wire:model="firstName" type="text" name="firstName" id="first-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="middle-name" class="font-semibold">Отчество (И. п.)</label>
                            <input wire:model="middleName" type="text" name="middleName" id="middle-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="last-name-genitive" class="font-semibold">Фамилия (Р. п.)<span class="text-red-400">*</span></label>
                            <input wire:model="lastNameGenitive" type="text" name="lastNameGenitive" id="last-name-genitive" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastNameGenitive') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="first-name-genitive" class="font-semibold">Имя (Р. п.)<span class="text-red-400">*</span></label>
                            <input wire:model="firstNameGenitive" type="text" name="firstNameGenitive" id="first-name-genitive" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstNameGenitive') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="middle-name-genitive" class="font-semibold">Отчество (Р. п.)</label>
                            <input wire:model="middleNameGenitive" type="text" name="middleNameGenitive" id="middle-name-genitive" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleNameGenitive') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="last-name-dative" class="font-semibold">Фамилия (Д. п.)<span class="text-red-400">*</span></label>
                            <input wire:model="lastNameDative" type="text" name="lastNameDative" id="last-name-dative" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="first-name-dative" class="font-semibold">Имя (Д. п.)<span class="text-red-400">*</span></label>
                            <input wire:model="firstNameDative" type="text" name="firstNameDative" id="first-name-dative" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="middle-name-dative" class="font-semibold">Отчество (Д. п.)</label>
                            <input wire:model="middleNameDative" type="text" name="middleNameDative" id="middle-name-dative" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('middleNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                        <div class="flex flex-col">
                            <label for="birthday" class="font-semibold">Дата рождения<span class="text-red-400">*</span></label>
                            <input wire:model="birthday" type="date" name="birthday" id="birthday" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('birthday') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="course" class="font-semibold">Курс<span class="text-red-400">*</span></label>
                            <input wire:model="course" type="text" name="course" id="course" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('course') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="teaching-form-name" class="font-semibold">Форма обучения<span class="text-red-400">*</span></label>
                            <input wire:model="teachingFormName" type="text" name="teachingFormName" id="teaching-form-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('teachingFormName') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="specialty-code" class="font-semibold">Шифр<span class="text-red-400">*</span></label>
                            <input wire:model="specialtyCode" type="text" name="specialtyCode" id="specialty-code" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('specialtyCode') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="speciality-name" class="font-semibold">Специальность<span class="text-red-400">*</span></label>
                        <input wire:model="specialityName" type="text" name="specialityName" id="speciality-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('specialityName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="order" class="text-sm font-semibold">Приказ о зачислении<span class="text-red-400">*</span></label>
                        <input wire:model="order" type="text" name="order" id="order" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('order') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="training-period" class="font-semibold">Срок обучения<span class="text-red-400">*</span></label>
                        <input wire:model="trainingPeriod" type="text" name="trainingPeriod" id="training-period" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('trainingPeriod') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div x-data="{ open: false }">
                            <div @click="open = !open" class="flex items-center justify-between bg-gray-200 border p-4">
                                <p>Для очно-заочной и заочной форм обучения</p>
                                <span :class="open ? 'fa-chevron-down' : 'fa-chevron-up'" class="fas"></span>
                            </div>
                            <div x-show="open" class="border p-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div class="flex flex-col">
                                        <label for="check-in-date" class="font-semibold">Период заезда с<span class="text-red-400">*</span></label>
                                        <input wire:model="checkInDate" type="date" name="checkInDate" id="check-in-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        @error('checkInDate') <span class="text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="check-out-date" class="font-semibold">Период заезда по<span class="text-red-400">*</span></label>
                                        <input wire:model="checkOutDate" type="date" name="checkOutDate" id="check-out-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        @error('checkOutDate') <span class="text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="check-in-duration" class="font-semibold">Продолжительность<span class="text-red-400">*</span></label>
                                        <input wire:model="checkInDuration" type="text" name="checkInDuration" id="check-in-duration" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        @error('checkInDuration') <span class="text-red-500">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label for="employment-place" class="font-semibold">Место работы<span class="text-red-500">*</span></label>
                                    <textarea wire:model="employmentPlace" name="employmentPlace" id="employment-place" cols="5" rows="2" placeholder="Укажите текущее место работы студента" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                                    @error('employmentPlace') <span class="text-red-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-1">
                        <label for="had-academic-leave" class="flex items-center font-semibold">
                            <input wire:model="hadAcademicLeave" type="checkbox" name="hadAcademicLeave" id="had-academic-leave" class="mr-1">
                            Предоставлялся академический отпуск
                        </label>
                        <label for="leave-orders" class="font-semibold">Приказы об академическом отпуске, выходе из него и об отчислении (по одному в строке)</label>
                        <textarea wire:model="leaveOrders" name="leaveOrders" id="leave-orders" rows="3" placeholder="Приказ о предоставлении академического отпуска от 17.02.2026 № 109/с;" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="py-2">
                        <label for="is-budget" class="flex items-center">
                            <input wire:model="isBudget" wire:key="is-budget-{{ $studentDocumentOrderRequestId }}" type="checkbox" name="isBudget" id="is-budget" class="mr-1">
                            Обучается на бюджете
                        </label>
                    </div>
                    <div class="flex flex-col">
                        <label for="signer" class="font-semibold">Выберите должность сотрудника, который будет подписывать документ<span class="text-red-500">*</span></label>
                        <select wire:model="signer" name="signer" id="signer" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Выберите должность сотрудника</option>
                            @foreach($listOfSigners as $key => $value)
                            <option value="{{ $key }}|{{ $value }}">{{ $key }}</option>
                            @endforeach
                        </select>
                        @error('signer') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <span class="text-red-500">*Поля обязательные для заполнения</span>
                </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isShowModalDialog')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click="update" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    <span wire:loading.remove wire:target="update">
                        {{ __('Подтвердить') }}
                    </span>
                    <span wire:loading wire:target="update">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>