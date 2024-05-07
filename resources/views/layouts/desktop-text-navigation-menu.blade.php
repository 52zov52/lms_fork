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
        <li class="{{ (request()->routeIs('my.publications')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('my.publications') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Список трудов</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('my.groups')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('my.groups') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Мои группы</span>
            </a>
        </li>
        @endrole
        @role('Student')
        <li class="{{ (request()->routeIs('document.order.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('document.order.requests') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Заказ документов (справок)</span>
            </a>
        </li>
        @endrole
        @role('Teacher')
        <li class="{{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('contingent') }}" class="w-10 h-8 inline-flex items-center px-2 py-3">
                <span class="whitespace-nowrap">Контингент</span>
            </a>
        </li>
        @endrole
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