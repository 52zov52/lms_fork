<nav class="overflow-x-hidden overflow-y-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
    <ul class="flex flex-col items-center space-y-2 px-2 py-2 text-sm">
        <li class="{{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('dashboard') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-house fa-lg"></i>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded hover:text-white">
            <a href="https://eiosiubip.ru/login/index.php" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-graduation-cap fa-lg"></i>
            </a>
        </li>
        <li class="{{ (request()->routeIs('elibrary')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('elibrary') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-book fa-lg"></i>
            </a>
        </li>
        <li class="{{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('schedule') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-calendar-days fa-lg"></i>
            </a>
        </li>
        @role('Student')
        <li class="{{ (request()->routeIs('student.my.disciplines')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.my.disciplines') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-list fa-lg"></i>
            </a>
        </li>
        <li class="{{ (request()->routeIs('student.document.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.document.requests') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-file-circle-plus fa-lg"></i>
            </a>
        </li>
        @endrole
        @can('access-to-admission-campaign')
        <li class="{{ (request()->routeIs('admission.campaign')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('admission.campaign') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-school fa-lg"></i>
            </a>
        </li>
        @endcan
        @can('show-operating-activity-and-decisions-rectorate-and-council')
        <li class="{{ (request()->routeIs('operating.activity')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('operating.activity') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-list-check fa-lg"></i>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.rectorate')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('decisions.rectorate') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-pen-nib fa-lg"></i>
            </a>
        </li>
        <li class="{{ (request()->routeIs('decisions.council')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('decisions.council') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-feather-pointed fa-lg"></i>
            </a>
        </li>
        @endcan
        @role('Teacher')
        <li class="{{ (request()->routeIs('digital.teacher.profile')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('digital.teacher.profile') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-chalkboard-user fa-lg"></i>
            </a>
        </li>
        @endrole
        @can('manage-educational-and-youth-policy-events')
        <li class="{{ (request()->routeIs('upbringing.management')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('upbringing.management') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-briefcase fa-lg"></i>
            </a>
        </li>
        @endcan
        @can('show-educational-and-youth-policy-events')
        <li class="{{ (request()->routeIs('youth.upbringing.event.info.report')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('youth.upbringing.event.info.report') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-business-time fa-lg"></i>
            </a>
        </li>
        @endcan
        @can('manage-student-document-requests')
        <li class="{{ (request()->routeIs('student.document.issuances')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.document.issuances') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-folder-closed fa-lg"></i>
            </a>
        </li>
        @endcan
        @can('access-to-contingent')
        <li class="{{ (request()->routeIs('contingent')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('contingent') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-people-group fa-lg"></i>
            </a>
        </li>
        @endcan
        @can('access-to-student-grades')
        <li class="{{ (request()->routeIs('student.grades')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('student.grades') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-diagram-project"></i>
            </a>
        </li>
        @endcan
        @can('access-to-data-on-students-academic-leaves')
        <li class="{{ (request()->routeIs('academic.leave.calendar')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('academic.leave.calendar') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-calendar-day fa-lg"></i>
            </a>
        </li>
        @endcan
        <li class="{{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <a href="{{ route('reports') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                <i class="fa-solid fa-chart-simple fa-lg"></i>
            </a>
        </li>
        <li class="hover:bg-blue-500 rounded hover:text-white">
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <a @click.prevent="$root.submit();" href="{{ route('logout') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
                    <i class="fa-solid fa-right-from-bracket fa-lg"></i>
                </a>
            </form>
        </li>
    </ul>
</nav>