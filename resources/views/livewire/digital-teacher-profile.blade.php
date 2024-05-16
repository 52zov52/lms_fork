<div class="flex flex-row space-x-2">
    @can('access-to-academic-workload')
    <a href="{{ route('academic.workload') }}">
        <div class="w-64 h-48 flex flex-col border border-slate-400/20 rounded text-lg hover:text-xl transition-all duration-500 cursor-pointer">
            <span class="w-full p-4 text-right">Учебная нагрузка</span>
        </div>
    </a>
    @endcan
    <a href="{{ route('my.publications') }}">
        <div class="w-64 h-48 flex flex-col border border-slate-400/20 rounded text-lg hover:text-xl transition-all duration-500 cursor-pointer">
            <span class="w-full p-4 text-right">Мой список трудов</span>
        </div>
    </a>
</div>