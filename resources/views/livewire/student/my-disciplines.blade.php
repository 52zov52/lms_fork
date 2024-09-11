<div class="flex flex-col space-y-2">

    <h4 class="font-semibold">Учебный год {{ $academicYear }}</h4>

    @if(!is_null($myDisciplines) && count($myDisciplines) != 0)
        @foreach($myDisciplines as $semester => $data)
        <div x-data="{ open: false }">
            <div @click="open = !open" class="flex items-center justify-between bg-gray-200 border p-4 select-none">
                <p>Семестр {{ $semester }}</p>
                <span :class="open ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
            </div>
            <div x-show.transition.in.duration.800ms="open" class="border p-4">
                <ul class="list-disc px-4">
                    @foreach($data as $value)
                    <li class="p-1">{{ $value['name'] }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    @else
    <x-alert type="info" title="Информация" message="Список дисциплин на текущий учебный год отсутствует" />
    @endif
</div>