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
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <button onclick="showToast('generateListOfStudentsInGroupReport')" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-2 text-xs text-left text-sky-400 hover:text-white font-bold">
                <i class="fa-solid fa-download"></i>
                Скачать список студентов
            </button>
        </div>
    </div>

    <div class="flex flex-row space-x-2">
        <input wire:model="ticketNumber" type="text" name="ticket_number" id="ticket-number-id" class="w-full border border-gray-300 rounded px-2 text-xs text-gray-900 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Для поиска введите № зачетной книжки">
        <button onclick="showToast('searchContingentByTicketNumbers')" class="w-12 bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-1 text-xs text-center text-blue-400 hover:text-white" title="">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
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
                    @can('access-to-print-defense-ministry-form-10')
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($contingent as $item)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $item->last_name }} {{ $item->first_name }} {{ $item->middle_name }}</td>
                    <td class="w-64 px-2 py-2 text-center">{{ $item->ticket_number }}</td>
                    <td class="w-64 px-2 py-2 text-center">{{ $item->state_name }}</td>
                    @can('access-to-print-defense-ministry-form-10')
                    <td class="w-32 px-2 py-2 text-center">
                        <button wire:click="printDefenseMinistryForm10({{ $item->user_id }})" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Скачать форму № 10">
                            <i class="fa-solid fa-download"></i>
                        </button>
                        <button wire:click="getIndividualStudentGradeReport({{ $item->user_id }})" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Ведомость индивидуальных оценок">
                            <i class="fa-solid fa-list"></i>
                        </button>
                    </td>
                    @endcan
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