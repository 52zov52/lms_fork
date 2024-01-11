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
        <select wire:model="groupId" wire:change="getContingent" name="group_id" id="group-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите группу из списка</option>
            @foreach($groups as $group)
            <option value="{{ $group->id }}">{{ $group->group_name }}</option>
            @endforeach
        </select>
        <button wire:click="generateListOfStudentsInGroupReport" class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-file-excel"></i>
            Скачать список студентов
        </button>
    </div>
    <div class="bg-white rounded shadow-xl p-4">
        <div class="tab-wrapper px-2 py-4" x-data="{ activeTab:  0 }">
            <ul class="flex bg-white text-sm md:text-base lg:text-base xl:text-base font-medium text-center divide-x divide-gray-200 border rounded-lg shadow-md">
                <li class="w-full">
                    <a href="#" @click="activeTab = 0" :class="{ 'active bg-gray-100': activeTab === 0 }" class="inline-block h-full w-full p-4 rounded-l-lg hover:bg-gray-100" aria-current="page">Списочный состав групп</a>
                </li>
                <li class="w-full">
                    <a href="#" @click="activeTab = 1" :class="{ 'active bg-gray-100': activeTab === 1 }" class="inline-block h-full w-full p-4 rounded-r-lg hover:bg-gray-100" aria-current="page">В академическом отпуске</a>
                </li>
            </ul>

            <div class="tab-panel mt-4" :class="{ 'active': activeTab === 0 }" x-show.transition.in.opacity.duration.600="activeTab === 0">
                @if(!is_null($contingent) && count($contingent) != 0)
                <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                    <tbody>
                        <tr class="bg-gray-100">
                            <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                            <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                            <td class="w-64 border px-2 py-2 font-semibold">№ зачетки</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Статус</td>
                        </tr>
                        @foreach($contingent as $item)
                        <tr>
                            <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto border px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                            <td class="w-64 border px-2 py-2">{{ $item->ticket_number }}</td>
                            <td class="w-64 border px-2 py-2">{{ $item->state_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div class="ml-3 text-base font-medium">
                        Информация отсутсвует. Для отображения данных выберите группу из списка.
                    </div>
                </div>
                @endif
            </div>

            <div class="tab-panel mt-4" :class="{ 'active': activeTab === 1 }" x-show.transition.in.opacity.duration.600="activeTab === 1">
                @can('access-to-data-on-students-academic-leaves')
                <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                    <tbody>
                        <tr class="bg-gray-100">
                            <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                            <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Группа</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Дата выхода из академ. отпуска</td>
                            <td class="w-64 border px-2 py-2 font-semibold">Комментарий</td>
                            <td class="w-40 border px-2 py-2 font-semibold">Действия</td>
                        </tr>
                        @foreach($studentsAcademicLeaves as $student)
                        <tr>
                            <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto border px-2 py-2">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                            <td class="w-64 border px-2 py-2">{{ $student->group_name }}</td>
                            <td class="w-64 border px-2 py-2">
                                @if(!empty($student->academic_leave_end_date))
                                {{ \Carbon\Carbon::parse($student->academic_leave_end_date)->format('d.m.Y') }}
                                @endif
                            </td>
                            <td class="w-64 border px-2 py-2">{{ $student->description }}</td>
                            <td class="w-40 border px-2 py-2 text-center">
                                <button wire:click="creatingAndEditingModalFormVisible(true, {{ $student->id }})" title="Редактировать" class="w-auto grow bg-sky-500 hover:bg-sky-600 rounded px-2 py-1 text-white">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button wire:click="redirectToDocumentsStudentsAcademicLeaves({{ $student->user_id }}, {{ $student->group_id }})" title="Оформление документов по академическому отпуску" class="w-auto grow bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                                    <i class="fa-solid fa-elevator"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endrole
            </div>
        </div>
    </div>

    <!-- Creating And Editing Modal Form -->
    <x-dialog-modal wire:model="isCreatingAndEditingModalFormVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Форма создания новой записи') : __('Форма редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="academic-leave-end-date-id">Дата выхода из академ. отпуска</label>
                        <input type="text" wire:model="academicLeaveEndDate" x-mask="99.99.9999" name="academic_leave_end_date" id="academic-leave-end-date-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="День.Месяц.Год">
                        @error('academicLeaveEndDate') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description-id">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col py-2">
                        <label for="is-academic-leave-id"> <input wire:model="isAcademicLeave" type="checkbox" name="is_academic_leave" id="is-academic-leave-id"> В академическом отпуске</label>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreatingAndEditingModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="{{ (!$isEdit) ? 'store' : 'update' }}" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Добавить') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>