<nav class="overflow-x-hidden overflow-y-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
    <ul class="flex flex-col space-y-2 px-2 py-2 text-sm">
        <li class="{{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('dashboard') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Главная</span>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded hover:text-white">
            <a href="https://eiosiubip.ru/login/index.php" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Система онлайн-обучения</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('elibrary')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('elibrary') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Электронные библиотечные системы</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('schedule') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Расписание</span>
            </a>
        </li>
        @role('Student')
        <li class="{{ (request()->routeIs('student.my.disciplines')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.my.disciplines') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Мои дисциплины</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.my.grades')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.my.grades') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Мои успеваемость</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.document.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.document.requests') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Заявки на справки</span>
            </a>
        </li>
        @endrole
        @can('access-to-admission-campaign')
        <li class="{{ (request()->routeIs('admission.campaign')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('admission.campaign') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Приемная кампания</span>
            </a>
        </li>
        @endcan
        @can('show-operating-activity-and-decisions-rectorate-and-council')
        <li class="{{ (request()->routeIs('operating.activity')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('operating.activity') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Операционная деятельность</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.rectorate')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('decisions.rectorate') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Решение ректората</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.council')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('decisions.council') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Решение уч. совета</span>
            </a>
        </li>
        @endcan
        @role('Teacher')
        <li class="{{ (request()->routeIs('digital.teacher.profile')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('digital.teacher.profile') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Цифровой профиль преподавателя</span>
            </a>
        </li>
        @endrole
        @can('manage-educational-and-youth-policy-events')
        <li class="{{ (request()->routeIs('upbringing.management')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('upbringing.management') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Управление мероприятиями по ВР и МП</span>
            </a>
        </li>
        @endcan
        @can('show-educational-and-youth-policy-events')
        <li class="{{ (request()->routeIs('youth.upbringing.event.info.report')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('youth.upbringing.event.info.report') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Мероприятия по ВР и МП</span>
            </a>
        </li>
        @endcan
        @can('manage-student-document-requests')
        <li class="{{ (request()->routeIs('student.document.issuances')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.document.issuances') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Обработка заявок на справки</span>
            </a>
        </li>
        @endcan
        @can('access-to-contingent')
        <li class="{{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('contingent') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Контингент</span>
            </a>
        </li>
        @endcan
        @can('access-to-student-grades')
        <li class="{{ (request()->routeIs('student.grades')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.grades') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Ведомости</span>
            </a>
        </li>
        @endcan
        @can('access-to-data-on-students-academic-leaves')
        <li class="{{ (request()->routeIs('academic.leave.calendar')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('academic.leave.calendar') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Календарь академ. отпусков</span>
            </a>
        </li>
        @endcan
        <li class="{{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('reports') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Отчеты и аналитика</span>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded hover:text-white">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a @click.prevent="$root.submit();" href="{{ route('logout') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                    <span class="whitespace-nowrap">Выход</span>
                </a>
            </form>
        </li>
    </ul>
</nav>