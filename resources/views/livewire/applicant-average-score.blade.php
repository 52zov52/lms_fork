<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-between">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <select wire:model="directionId" wire:change="setDirectionId" name="direction_id" id="direction-id" class="w-full md:w-64 bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите направление подготовки</option>
                @foreach($directions as $direction)
                <option value="{{ $direction->id }}">
                    {{ $direction->speciality_code }} {{ $direction->speciality_name }}
                    @if(!is_null($direction->speciality_profile)) ({{ $direction->speciality_profile }}) @endif
                    @if($direction->teaching_form_id == 1)
                    Очная
                    @elseif($direction->teaching_form_id == 2)
                    Заочная
                    @else
                    Очно-заочная
                    @endif
                    @if($direction->funding_type == 1)
                    (К)
                    @else
                    (Б)
                    @endif
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
            @can('access-to-enter-admission-campaign')
            <button wire:click="setEntryModalVisible(false)" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-left text-green-400 hover:text-white font-bold">
                <i class="fa-solid fa-circle-plus fa-lg"></i>
                Добавить запись
            </button>
            @endcan
            <button wire:click="print()" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
                <i class="fa-solid fa-print fa-lg"></i>
                Печать списка абитуриентов
            </button>
        </div>
    </div>

    <div class="flex flex-col space-y-2">
        <p class="text-xs lg:text-sm">Общий средний балл направления подготовки: {{ $totalAverageScore }} (на базе 9 кл. {{ $totalCompletedNineClassAverageScore }}; на базе 11 кл. {{ $totalCompletedElevenClassAverageScore }})</p>
        <p class="text-xs lg:text-sm">Средний балл направления подготовки по договорам: {{ $contractAverageScore }} (на базе 9 кл. {{ $contractCompletedNineClassAverageScore }}; на базе 11 кл. {{ $contractCompletedElevenClassAverageScore }})</p>
    </div>

    @if(!is_null($data) && count($data) != 0)
    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th scope="col" class="w-40 px-2 py-2 text-left font-semibold">Тел.</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Класс</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Кол-во 3</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Кол-во 4</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Кол-во 5</th>
                    <th scope="col" class="w-24 px-2 py-2 font-semibold">Средний балл</th>
                    <th scope="col" class="w-24 px-2 py-2 font-semibold">Договор</th>
                    @can('access-to-enter-admission-campaign')
                    <th scope="col" class="w-24 px-2 py-2 font-semibold">Действия</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                    <td class="w-40 px-2 py-2 text-left">{{ $item->phone }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $item->completed_classes_count }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $item->threes_count }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $item->fours_count }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $item->fives_count }}</td>
                    <td class="w-24 px-2 py-2 text-center">{{ $item->average_score }}</td>
                    <td class="w-24 px-2 py-2 text-center">
                        @if($item->is_contracted)
                        Да
                        @else
                        Нет
                        @endif
                    </td>
                    @can('access-to-enter-admission-campaign')
                    <td class="w-24 px-2 py-2 text-center">
                        <div class="grid gap-1 grid-cols-2">
                            <button wire:click="setEntryModalVisible(true, {{ $item->id }})" class="bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="confirmDestroy('{{ $item->id }}')" class="bg-white hover:bg-red-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 px-2 py-1 text-xs text-center text-red-400 hover:text-white" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Mobile -->
    <div class="lg:hidden flex flex-col space-y-2">
        @foreach($data as $item)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
            <div class="flex flex-col">
                <span class="font-semibold">ФИО:</span>
                <span>{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Телефон:</span>
                <span>{{ $item->phone }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Класс:</span>
                <span>{{ $item->completed_classes_count }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Кол-во 3:</span>
                <span>{{ $item->threes_count }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Кол-во 4:</span>
                <span>{{ $item->fours_count }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Кол-во 5:</span>
                <span>{{ $item->fives_count }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Средний балл:</span>
                <span>{{ $item->average_score }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Договор:</span>
                <span>
                    @if($item->is_contracted)
                    Да
                    @else
                    Нет
                    @endif
                </span>
            </div>
            @can('access-to-enter-admission-campaign')
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <div class="flex flex-row space-x-2">
                    <button wire:click="setEntryModalVisible(true, {{ $item->id }})" class="w-auto grow bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white font-bold" title="" data-tippy-content="Редактировать">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button onclick="confirmDestroy('{{ $item->id }}')" class="w-auto grow bg-white hover:bg-red-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 px-2 py-1 text-xs text-center text-red-400 hover:text-white font-bold" title="" data-tippy-content="Удалить">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
            @endcan
        </div>
        @endforeach
    </div>
    <div class="mt-2">
        {{ $data->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Средние баллы аттестата абитуриентов не указаны. Чтобы добавить новую запись, пожалуйста, нажмите кнопку «Добавить запись»" />
    @endif

    <!-- Entry Modal -->
    <x-dialog-modal wire:model="isEntryModalVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Добавление записи') : __('Редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="admission-campaign-direction-id" class="font-semibold">Направление подготовки<span class="ml-1 text-red-400">*</span></label>
                        <select wire:model="admissionCampaignDirectionId" name="admission_campaign_direction_id" id="admission-campaign-direction-id" class="w-full border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите направление подготовки</option>
                            @foreach($directions as $value)
                            <option value="{{ $value->id }}">
                                {{ $value->speciality_code }} {{ $value->speciality_name }}
                                @if(!is_null($value->speciality_profile)) ({{ $value->speciality_profile }}) @endif
                                @if($value->teaching_form_id == 1)
                                Очная
                                @elseif($value->teaching_form_id == 2)
                                Заочная
                                @else
                                Очно-заочная
                                @endif
                                @if($value->funding_type == 1)
                                (К)
                                @else
                                (Б)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('admissionCampaignDirectionId') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="last-name" class="font-semibold">Фамилия<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="lastName" type="text" name="last_name" id="last-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('lastName') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="first-name" class="font-semibold">Имя<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="firstName" type="text" name="first_name" id="first-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('firstName') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="middle-name" class="font-semibold">Отчество</label>
                            <input wire:model="middleName" type="text" name="middle_name" id="middle-name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="phone" class="font-semibold">Телефон</label>
                            <input wire:model="phone" type="text" name="phone" id="phone" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex flex-col">
                            <label for="email" class="font-semibold">Email</label>
                            <input wire:model="email" type="text" name="email" id="email" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="flex flex-col">
                            <label for="threes-count" class="font-semibold">Кол-во 3</label>
                            <input wire:model="threesCount" type="text" name="threes_count" id="threes-count" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('threesCount') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="fours-count" class="font-semibold">Кол-во 4</label>
                            <input wire:model="foursCount" type="text" name="fours_count" id="fours-count" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('foursCount') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="fives-count" class="font-semibold">Кол-во 5</label>
                            <input wire:model="fivesCount" type="text" name="fives_count" id="fives-count" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('fivesCount') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-row space-x-2">
                        <div class="flex space-x-1 items-center">
                            <input wire:model="completedClassesCount" type="radio" id="nine-classes" value="9">
                            <label for="nine-classes" class="cursor-pointer">9 класс</label>
                        </div>
                        <div class="flex space-x-1 items-center">
                            <input wire:model="completedClassesCount" type="radio" id="eleven-classes" value="11">
                            <label for="eleven-classes" class="cursor-pointer">11 класс</label>
                        </div>
                    </div>
                    <div class="flex flex-col py-2">
                        <label for="is-contracted"> <input wire:model="is_contracted" type="checkbox" name="is_contracted" id="is-contracted"> Договор</label>
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
        await @this.callMethod(methodName, param);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }

    function confirmDestroy(id) {
        if (confirm('Вы уверены, что хотите удалить данную запись?')) {
            showToast('destroy', id);
        }
    }
</script>
@endpush