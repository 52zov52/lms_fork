<nav class="overflow-x-hidden overflow-y-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
    <ul class="flex flex-col space-y-2 px-2 py-2 text-sm" :class="{ 'items-center': !isExpandSideBar && !isSideBarOpen }">
        <li class="{{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('dashboard') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-house fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Главная</span>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded hover:text-white">
            <a href="https://eiosiubip.ru/login/index.php" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-graduation-cap fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Система онлайн-обучения</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('elibrary')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('elibrary') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-book fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Электронные библиотечные системы</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('schedule') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-calendar-days fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Расписание</span>
            </a>
        </li>
        @role('Student')
        <li class="{{ (request()->routeIs('student.my.disciplines')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.my.disciplines') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-list fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Мои дисциплины</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.my.grades')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.my.grades') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-shapes fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Моя успеваемость</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.spt.consent.status')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.spt.consent.status') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-clipboard-check fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Согласие на СПТ</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.document.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.document.requests') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-file-circle-plus fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Заявки на справки</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.social.passport')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.social.passport') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-passport fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Социальный паспорт обучающегося</span>
            </a>
        </li>
        @endrole
        @can('access-to-admission-campaign')
        <li class="{{ (request()->routeIs('admission.campaign')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('admission.campaign') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-school fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Приемная кампания</span>
            </a>
        </li>
        @endcan
        @can('show-operating-activity-and-decisions-rectorate-and-council')
        <li class="{{ (request()->routeIs('operating.activity')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('operating.activity') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-list-check fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Операционная деятельность</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.rectorate')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('decisions.rectorate') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-pen-nib fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Решение ректората</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.council')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('decisions.council') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-feather-pointed fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Решение уч. совета</span>
            </a>
        </li>
        @endcan
        @role('Teacher')
        <li class="{{ (request()->routeIs('digital.teacher.profile')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('digital.teacher.profile') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-chalkboard-user fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Цифровой профиль преподавателя</span>
            </a>
        </li>
        @endrole
        @can('manage-educational-and-youth-policy-events')
        <li class="{{ (request()->routeIs('upbringing.management')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('upbringing.management') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-briefcase fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Управление мероприятиями по ВР и МП</span>
            </a>
        </li>
        @endcan
        @can('show-educational-and-youth-policy-events')
        <li class="{{ (request()->routeIs('youth.upbringing.event.info.report')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('youth.upbringing.event.info.report') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-business-time fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Мероприятия по ВР и МП</span>
            </a>
        </li>
        @endcan
        @can('manage-student-document-requests')
        <li class="{{ (request()->routeIs('student.document.issuances')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.document.issuances') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-folder-closed fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Обработка заявок на справки</span>
            </a>
        </li>
        @endcan
        @can('access-to-contingent')
        <li class="{{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('contingent') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-people-group fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Контингент</span>
            </a>
        </li>
        @endcan
        @can('access-to-student-social-passport-review')
        <li class="{{ (request()->routeIs('employee.student.social.passport.review')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('employee.student.social.passport.review') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-user-check fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Соц. паспорта на утверждение</span>
            </a>
        </li>
        @endcan
        @can('access-to-student-grades')
        <li class="{{ (request()->routeIs('student.grades')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.grades') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-diagram-project"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Ведомости</span>
            </a>
        </li>
        @endcan
        @can('access-to-data-on-students-academic-leaves')
        <li class="{{ (request()->routeIs('academic.leave.calendar')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('academic.leave.calendar') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-calendar-day fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Календарь академ. отпусков</span>
            </a>
        </li>
        @endcan
        <li class="{{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('reports') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-chart-simple fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Отчеты и аналитика</span>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded hover:text-white">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a @click.prevent="$root.submit();" href="{{ route('logout') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                    <i class="fa-solid fa-right-from-bracket fa-lg"></i>
                    <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Выход</span>
                </a>
            </form>
        </li>
    </ul>
</nav>