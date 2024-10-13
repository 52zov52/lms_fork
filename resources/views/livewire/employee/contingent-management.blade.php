<div x-data="{ hovered: null }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
    <a x-bind:class="{ 'hovered': hovered === 'contingent' }" @mouseover="hovered = 'contingent'" @mouseout="hovered = null" class="w-auto" href="#">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Контингент</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                В разделе представлены данные о составе обучающихся, их распределении по группам, о переводах, отчислениях и динамике изменений за указанный период
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'contingent', 'w-6': hovered != 'contingent' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'bulletins' }" @mouseover="hovered = 'bulletins'" @mouseout="hovered = null" class="w-auto" href="#">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Ведомости</div>
            <div class="grow px-4 py-2 text-sm text-justify">
                В разделе представлены данные об успеваемости студентов по промежуточной аттестации и текущему контролю
            </div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'bulletins', 'w-6': hovered != 'bulletins' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'student_academic_leave_calendar' }" @mouseover="hovered = 'student_academic_leave_calendar'" @mouseout="hovered = null" class="w-auto" href="#">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Календарь академических отпусков</div>
            <div class="grow px-4 py-2 text-sm text-justify">В разделе представлены данные о периодах, на которые студентам предоставляется временное разрешение прекратить обучение по уважительным причинам</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'student_academic_leave_calendar', 'w-6': hovered != 'student_academic_leave_calendar' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'student_document_order_issuances' }" @mouseover="hovered = 'student_document_order_issuances'" @mouseout="hovered = null" class="w-auto" href="#">
        <div class="w-full h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Обработка заявок на документы</div>
            <div class="grow px-4 py-2 text-sm text-justify">В разделе представлены данные о статусе и прогрессе обработки заявок на получение различных документов обучающимися</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'student_document_order_issuances', 'w-6': hovered != 'student_document_order_issuances' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
</div>