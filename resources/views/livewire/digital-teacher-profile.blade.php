<div class="flex flex-col space-y-2">
    <ul class="list-disc px-4">
        @can('access-to-academic-workload')
        <li>
            <a href="{{ route('academic.workload') }}" class="text-blue-500 hover:underline">Учебная нагрузка</a>
        </li>
        @endcan
        <li>
            <a href="{{ route('my.publications') }}" class="text-blue-500 hover:underline">Список трудов</a>
        </li>
    </ul>
</div>
