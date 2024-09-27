<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-between">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <select wire:model="groupId" wire:change="getContingent" name="group_id" id="group-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите группу из списка</option>
                @foreach($groups as $group)
                <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(!is_null($contingent) && count($contingent) != 0)
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th scope="col" class="w-64 px-2 py-2 font-semibold">№ зачетки</th>
                    <th scope="col" class="w-64 px-2 py-2 font-semibold">Статус</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contingent as $item)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                    <td class="w-64 px-2 py-2 text-center">{{ $item->ticket_number }}</td>
                    <td class="w-64 px-2 py-2 text-center">
                        @if(is_null($item->questionnaire_status) || $item->questionnaire_status == 0)
                        <span class="text-sky-400 font-bold">На редактировании</span>
                        @elseif($item->questionnaire_status == 1)
                        <span class="text-orange-400 font-bold">На подтверждении</span>
                        @else
                        <span class="text-green-400 font-bold">Подтверждена</span>
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        <div class="flex flex-row items-center justify-center space-x-1">
                            @if(is_null($item->questionnaire_status) || $item->questionnaire_status == 0)
                            -
                            @elseif($item->questionnaire_status == 1)
                            <button onclick="showToast('download', '{{ $item->user_id }}')" class="w-9 bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Скачать социальный паспорт">
                                <i class="fa-solid fa-passport"></i>
                            </button>
                            @can('confirm-student-social-passport')
                            <button onclick="confirmSocialPassport('{{ $item->user_id }}')" class="w-9 bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-1 text-xs text-center text-green-400 hover:text-white" title="" data-tippy-content="Подтвердить">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button onclick="showToast('backToEdit', '{{ $item->user_id }}')" class="w-9 bg-white hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-gray-400 px-2 py-1 text-xs text-center text-gray-400 hover:text-white" title="" data-tippy-content="Вернуть на редактирование">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                            @endcan
                            @else
                            <button onclick="showToast('download', '{{ $item->user_id }}')" class="w-9 bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Скачать социальный паспорт">
                                <i class="fa-solid fa-passport"></i>
                            </button>
                            @can('confirm-student-social-passport')
                            <button onclick="showToast('backToEdit', '{{ $item->user_id }}')" class="w-9 bg-white hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-gray-400 px-2 py-1 text-xs text-center text-gray-400 hover:text-white" title="" data-tippy-content="Вернуть на редактирование">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="lg:hidden flex flex-col space-y-2">
        @foreach($contingent as $item)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
            <div class="flex flex-col">
                <span class="font-semibold">ФИО:</span>
                <span>{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">№ зачетки:</span>
                <span>{{ $item->ticket_number }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Статус:</span>
                @if(is_null($item->questionnaire_status) || $item->questionnaire_status == 0)
                <span class="text-sky-400 font-bold">На редактировании</span>
                @elseif($item->questionnaire_status == 1)
                <span class="text-orange-400 font-bold">На подтверждении</span>
                @else
                <span class="text-green-400 font-bold">Подтверждена</span>
                @endif
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <div class="flex flex-row space-x-2">
                    @if(is_null($item->questionnaire_status) || $item->questionnaire_status == 0)
                    -
                    @elseif($item->questionnaire_status == 1)
                    <button onclick="showToast('download', '{{ $item->user_id }}')" class="w-auto grow bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Скачать социальный паспорт">
                        <i class="fa-solid fa-passport"></i>
                    </button>
                    <button onclick="confirmSocialPassport('{{ $item->user_id }}')" class="w-auto grow bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-1 text-xs text-center text-green-400 hover:text-white" title="" data-tippy-content="Подтвердить">
                        <i class="fa-solid fa-check"></i>
                    </button>
                    <button onclick="showToast('backToEdit', '{{ $item->user_id }}')" class="w-auto grow bg-white hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-gray-400 px-2 py-1 text-xs text-center text-gray-400 hover:text-white" title="" data-tippy-content="Вернуть на редактирование">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                    @else
                    <button onclick="showToast('download', '{{ $item->user_id }}')" class="w-auto grow bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Скачать социальный паспорт">
                        <i class="fa-solid fa-passport"></i>
                    </button>
                    <button onclick="showToast('backToEdit', '{{ $item->user_id }}')" class="w-auto grow bg-white hover:bg-gray-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-gray-400 px-2 py-1 text-xs text-center text-gray-400 hover:text-white" title="" data-tippy-content="Вернуть на редактирование">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <x-alert type="info" title="Информация" message="Для просмотра списка студентов, направивших социальный паспорт на утверждение, выберите нужную группу из списка" />
    @endif

</div>

@push('scripts')
<script>
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

    function confirmSocialPassport($userId) {
        if (confirm('Вы уверены, что хотите подтвердить социальный паспорт?')) {
            showToast('confirm', $userId);
        }
    }
</script>
@endpush