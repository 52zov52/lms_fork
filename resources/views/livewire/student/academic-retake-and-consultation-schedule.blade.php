<div x-data="{ hovered: null }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
    <a x-bind:class="{ 'hovered': hovered === 'academic_retake_schedule' }" @mouseover="hovered = 'academic_retake_schedule'" @mouseout="hovered = null" class="w-auto" href="{{ route('student.academic.retake.schedule.list') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">График пересдач академических задолженностей</div>
            <div class="grow px-4 py-2 text-sm text-justify">
            В разделе отражается расписание пересдач для студентов, имеющих академические задолженности, с указанием аудиторий, дат и времени проведения экзаменов
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'academic_retake_schedule', 'w-6': hovered != 'academic_retake_schedule' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'consultation_schedule' }" @mouseover="hovered = 'consultation_schedule'" @mouseout="hovered = null" class="w-auto" href="{{ route('student.my-consultation.schedule') }}">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">График консультаций</div>
            <div class="grow px-4 py-2 text-sm text-justify">
            В разделе отражается график консультаций, с указанием дисциплин/практик, аудиторий, дат и времени проведения консультаций
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'consultation_schedule', 'w-6': hovered != 'consultation_schedule' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
</div>