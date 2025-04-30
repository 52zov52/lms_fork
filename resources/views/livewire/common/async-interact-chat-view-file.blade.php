<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            Группа: {{ $group }}
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Дисциплина: {{ $discipline }} ({{ $normative }})
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Учебный год: {{ $studyYear }} {{ $halfYear }}-е полугодие
        </h4>
        @if(count($materials) != 0)
            @foreach($materials as $material)
            <div class="bg-gray-100 dark:bg-slate-700 flex flex-col space-y-2 border rounded border-slate-400/20 px-4 py-2">
                <p class="text-sm font-semibold">{{ $material['fio'] }}</p>
                <p class="text-sm">{{ $material['created_at'] }}</p>
                <div class="text-sm">
                    {!! nl2br(e($material['comment'])) !!}
                </div>
                @if(!is_null($material['files']))
                <div class="flex flex-col space-y-2 border rounded border-slate-400/20 px-4 py-2">
                    <p class="text-sm font-semibold">Прикреплённые файлы:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($material['files'] as $value)
                        <li>
                            <a class="text-blue-500 hover:underline" href="{{ asset('storage/' . $value['path_to_file']) }}" target="_blank" rel="noopener noreferrer">{{ $value['original_filename'] }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endforeach
        @else
        <x-alert type="info" title="Информация" message="Учебный материал по дисциплине не загружен" />
        @endif
    </div>
</div>
