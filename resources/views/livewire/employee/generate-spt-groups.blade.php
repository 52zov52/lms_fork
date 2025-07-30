<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row md:justify-between space-y-2 md:space-y-0">
            <div class="flex flex-row space-x-2">
                <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В процессе</span>
                <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Выполнена</span>
                <span class="bg-red-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Ошибка</span>
            </div>

            <button wire:click="generateUnconfirmedConsents" class="w-auto md:w-36 btn-add">
                <span wire:loading.remove wire:target="generateUnconfirmedConsents">
                    <i class="fa-solid fa-circle-plus"></i>
                    Создать задачу
                </span>
                <span wire:loading wire:target="generateUnconfirmedConsents">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </span>
            </button>
        </div>

        <div wire:poll.5s="checkStatuses">
            @if(!is_null($generateSptGroupsJobs) && count($generateSptGroupsJobs) != 0)
            <div class="border rounded border-slate-400/20">
                <table class="w-full text-xs md:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">UUID задачи</th>
                            <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($generateSptGroupsJobs as $job)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto px-2 py-2 text-left">{{ $job->job_uuid }}</td>
                            <td class="w-32 px-2 py-2 text-center">
                                @if($job->status == 0)
                                <span class="text-orange-400 font-bold cursor-pointer">П</span>
                                @elseif($job->status == 1)
                                <span class="text-green-400 font-bold cursor-pointer">В</span>
                                @else
                                <span class="text-red-400 font-bold cursor-pointer">О</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <x-alert type="info" title="Информация" message="Список задач на генерацию групп для СПТ отсутствует. Для создания задачи нажмите на кнопку «Создать задачу»" />
            @endif
        </div>
        <div class="text-red-400 text-xs font-semibold">
            <p>* Задача формирует список студентов для участия в СПТ на текущий учебный год (с 1 сентября по 31 августа).</p>
            <ul class="list-inside list-disc">
                <li>Одна задача = один учебный год.</li>
                <li>При повторном создании задачи в том же учебном году, в список автоматически добавляются студенты, зачисленные после предыдущего запуска.</li>
                <li>Для нового учебного года необходимо создать отдельную задачу после 1 сентября.</li>
            </ul>
        </div>
    </div>
</div>