<nav class="overflow-x-hidden overflow-y-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
    <ul class="flex flex-col space-y-2 px-2 py-2 text-sm" :class="{ 'items-center': !isExpandSideBar && !isSideBarOpen }">
        <li class="{{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('dashboard') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-house fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Главная</span>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded px-2.5 py-2 hover:text-white">
            <a href="https://eiosiubip.ru/login/index.php" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-graduation-cap fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Система онлайн-обучения</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('elibrary')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('elibrary') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-book fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Электронные библиотечные системы</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('schedule') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-calendar-days fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Расписание</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('operating.activity')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('operating.activity') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-list-check fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Операционная деятельность</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.rectorate')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('decisions.rectorate') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-pen-nib fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Решение ректората</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.council')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('decisions.council') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-feather-pointed fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Решение уч. совета</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('my.groups')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('my.groups') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-user-group fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Мои группы</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('document.order.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('document.order.requests') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-file-invoice fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Заказ документов (справок)</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('contingent') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-people-group fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Контингент</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('academic.leave.calendar')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('academic.leave.calendar') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-calendar-day fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Календарь академ. отпусков</span>
            </a>
        </li>
        <li class="{{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded px-2.5 py-2">
            <a href="{{ route('reports') }}" class="w-full inline-flex items-baseline">
                <i class="fa-solid fa-chart-simple fa-lg"></i>
                <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Отчеты и аналитика</span>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded px-2.5 py-2 hover:text-white">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a @click.prevent="$root.submit();" href="{{ route('logout') }}" class="w-full inline-flex items-baseline">
                    <i class="fa-solid fa-right-from-bracket fa-lg"></i>
                    <span x-show="isExpandSideBar || isSideBarOpen" class="ml-2 whitespace-nowrap">Выход</span>
                </a>
            </form>
        </li>
    </ul>
</nav>