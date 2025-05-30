<div x-data="{ hovered: null }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
    @can('access-to-e-journal')
    <a x-bind:class="{ 'hovered': hovered === 'e_journal' }" @mouseover="hovered = 'e_journal'" @mouseout="hovered = null" class="w-auto" href="{{ route('teacher.my.groups') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Электронный журнал</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                В разделе представлена это цифровая версия журнала, в которой отмечаются пропуски, оценки, указываются темы занятий
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'e_journal', 'w-6': hovered != 'e_journal' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    @endcan
    <a x-bind:class="{ 'hovered': hovered === 'teacher_async_interact' }" @mouseover="hovered = 'teacher_async_interact'" @mouseout="hovered = null" class="w-auto" href="{{ route('teacher.async.interact.chats') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Асинхронное взаимодействие</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                Раздел предназначен для асинхронного взаимодействия со студентами путем обмена учебными материалами и проверки выполнения их работ
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'teacher_async_interact', 'w-6': hovered != 'teacher_async_interact' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    @can('access-to-academic-workload')
    <a x-bind:class="{ 'hovered': hovered === 'academic_workload' }" @mouseover="hovered = 'academic_workload'" @mouseout="hovered = null" class="w-auto" href="{{ route('teacher.academic.workload') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Учебная нагрузка</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                В разделе представлены данные о дисциплинах, количестве часов и видах работ за определённый период
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'academic_workload', 'w-6': hovered != 'academic_workload' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    @endcan
    <a x-bind:class="{ 'hovered': hovered === 'my_publications' }" @mouseover="hovered = 'my_publications'" @mouseout="hovered = null" class="w-auto" href="{{ route('my.publications') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Мой список трудов</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                В разделе представлены данные о научных трудах, статьях и исследованиях, выполненных в определенный период времени или в рамках определенной тематики
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'my_publications', 'w-6': hovered != 'my_publications' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'list_publications' }" @mouseover="hovered = 'list_publications'" @mouseout="hovered = null" class="w-auto" href="{{ route('list.publications') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Список научных трудов сотрудников Университета</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                В разделе представлены данные о научных трудах, статьях и исследованиях, выполненных сотрудниками Университета в определенный период времени или в рамках определенной тематики
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'list_publications', 'w-6': hovered != 'list_publications' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'academic_retake_schedule' }" @mouseover="hovered = 'academic_retake_schedule'" @mouseout="hovered = null" class="w-auto" href="{{ route('teacher.academic.retake.schedule') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">График пересдач академических задолженностей студентов</div>
            <div class="grow px-4 py-2 text-sm text-justify">
            В разделе указывается расписание пересдач для студентов, имеющих академические задолженности, с указанием аудиторий, дат и времени проведения экзаменов
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'academic_retake_schedule', 'w-6': hovered != 'academic_retake_schedule' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'consultation_schedule' }" @mouseover="hovered = 'consultation_schedule'" @mouseout="hovered = null" class="w-auto" href="{{ route('teacher.consultation.schedule') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">График консультаций студентов</div>
            <div class="grow px-4 py-2 text-sm text-justify">
            В разделе указывается график консультаций для студентов, с указанием аудиторий, дат и времени проведения консультаций
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'consultation_schedule', 'w-6': hovered != 'consultation_schedule' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
</div>