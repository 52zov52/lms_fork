<div x-data="{ hovered: null }" class="flex flex-row flex-wrap">
    <a x-bind:class="{ 'hovered': hovered === 'academic_workload' }" @mouseover="hovered = 'academic_workload'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('student.social.passport') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Анкета "Социальный паспорт обучающегося"</div>
            <div class="grow px-4 py-2 text-sm text-justify">Анкета представляет собой индивидуальный профиль каждого обучающегося, содержащий информацию о его образовательном пути, социальном окружении, интересах и потребностях.</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'academic_workload', 'w-6': hovered != 'academic_workload' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
</div>