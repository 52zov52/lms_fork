<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @if(!is_null($consultationSchedules) && count($consultationSchedules) != 0)
        <div class="hidden lg:block border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 font-semibold">№ пп</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Дисциплина/практика</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">Дата</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">Время</th>
                        <th scope="col" class="w-32 px-2 py-2 font-semibold">Аудитория</th>
                        <th scope="col" class="w-64 px-2 py-2 font-semibold">Комментарий</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consultationSchedules as $consultation)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto px-2 py-2 text-left">{{ $consultation->name }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('d.m.Y') }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $consultation->consultation_time }}</td>
                        <td class="w-32 px-2 py-2 text-center">{{ $consultation->room }}</td>
                        <td class="w-64 px-2 py-2 text-left">{{ $consultation->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="lg:hidden flex flex-col space-y-2">
            @foreach($consultationSchedules as $consultation)
            <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Дисциплина/практика:</span>
                    <span>{{ $consultation->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата консультации:</span>
                    <span>{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('d.m.Y') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Время консультации:</span>
                    <span>{{ $consultation->consultation_time }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Аудитория:</span>
                    <span>{{ $consultation->room }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Комментарий:</span>
                    <span>{{ $consultation->description }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $consultationSchedules->links() }}
        </div>
        @else
        <x-alert type="info" title="Информация" message="График консультаций на текущий учебный год отсутствует" />
        @endif
    </div>
</div>