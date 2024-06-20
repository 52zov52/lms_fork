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
        <li class="{{ (request()->routeIs('my.groups')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('my.groups') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-user-group fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Мои группы</span>
            </a>
        </li>
        @endrole
        @role('Student')
        <li class="{{ (request()->routeIs('document.order.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('document.order.requests') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-file-invoice fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Заказ документов (справок)</span>
            </a>
        </li>
        @endrole
        @can('access-to-contingent')
        <li class="{{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('contingent') }}" class="space-x-2 px-2 py-3" :class="{ 'w-10 h-8 flex items-center justify-center': !isExpandSideBar && !isSideBarOpen, 'inline-flex items-baseline': isExpandSideBar || isSideBarOpen}">
                <i class="fa-solid fa-people-group fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="whitespace-nowrap">Контингент</span>
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