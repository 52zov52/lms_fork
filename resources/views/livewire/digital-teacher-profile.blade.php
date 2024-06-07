<div x-data="{ hovered: null }" class="flex flex-row flex-wrap">
    @can('access-to-academic-workload')
    <a x-bind:class="{ 'hovered': hovered === 'academic_workload' }" @mouseover="hovered = 'academic_workload'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('academic.workload') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Учебная нагрузка</div>
            <div class="grow px-4 py-2 text-sm text-justify">В разделе представлены данные о дисциплинах, количестве часов и видах работ за определённый период</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'academic_workload', 'w-6': hovered != 'academic_workload' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    @endcan
    <a x-bind:class="{ 'hovered': hovered === 'my_publications' }" @mouseover="hovered = 'my_publications'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('my.publications') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Мой список трудов</div>
            <div class="grow px-4 py-2 text-sm text-justify">В разделе представлены данные о научных трудах, статьях и исследованиях, выполненных в определенный период времени или в рамках определенной тематики</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'my_publications', 'w-6': hovered != 'my_publications' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'list_publications' }" @mouseover="hovered = 'list_publications'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('list.publications') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Список научных трудов сотрудников Университета</div>
            <div class="grow px-4 py-2 text-sm text-justify">В разделе представлены данные о научных трудах, статьях и исследованиях, выполненных сотрудниками Университета в определенный период времени или в рамках определенной тематики</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'list_publications', 'w-6': hovered != 'list_publications' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
</div>