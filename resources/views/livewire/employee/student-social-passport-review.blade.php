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
    <div class="border rounded border-slate-400/20">
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
                        @if(is_null($item->questionnaire_status) || $item->questionnaire_status == 0)
                        -
                        @elseif($item->questionnaire_status == 1)
                        <div class="grid gap-1 grid-cols-3">
                            <button onclick="showToast('download', '{{ $item->user_id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-gray-400 font-bold" title="" data-tippy-content="Скачать социальный паспорт">
                                <i class="fa-solid fa-passport"></i>
                            </button>
                            <button onclick="confirmSocialPassport('{{ $item->user_id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-green-400 font-bold" title="" data-tippy-content="Подтвердить">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button onclick="showToast('backToEdit', '{{ $item->user_id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-gray-400 font-bold" title="" data-tippy-content="Вернуть на редактирование">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                        @else
                        <div class="grid gap-1 grid-cols-2">
                            <button onclick="showToast('download', '{{ $item->user_id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-gray-400 font-bold" title="" data-tippy-content="Скачать социальный паспорт">
                                <i class="fa-solid fa-passport"></i>
                            </button>
                            <button onclick="showToast('backToEdit', '{{ $item->user_id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-gray-400 font-bold" title="" data-tippy-content="Вернуть на редактирование">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <x-alert type="info" title="Информация" message="Для просмотра списочного состава учебной группы, выберите нужную группу из списка" />
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