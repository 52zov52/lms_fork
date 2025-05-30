<!-- Logo -->
<div :class="{ 'justify-center': !isMobileMenuVisible, 'justify-between px-4': isMobileMenuVisible }" class="flex items-center py-2.5">
    <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2': isMobileMenuVisible }" class="flex items-center">
        <img id="logo" x-bind:src="logo" class="w-12 h-8">
        <span x-show="isMobileMenuVisible" class="md:hidden font-semibold whitespace-nowrap">Личный кабинет</span>
    </div>
    <button x-show="isMobileMenuVisible" @click="isMobileMenuVisible = !isMobileMenuVisible" class="md:hidden hover:text-blue-500">
        <i class="fa-solid fa-xmark fa-lg"></i>
    </button>
</div>
<!-- Menu -->
<div class="mt-4 flex flex-col space-y-2 px-2.5">
    <a href="{{ route('dashboard') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-home fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Главная</span>
        </div>
    </a>
    <a href="https://eiosiubip.ru/login/index.php">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center hover:bg-blue-500 hover:text-white rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-graduation-cap fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Система онлайн-обучения</span>
        </div>
    </a>
    <a href="{{ route('elibrary') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('elibrary')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-book fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Электронные библиотечные системы</span>
        </div>
    </a>
    <a href="{{ route('schedule') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Расписание</span>
        </div>
    </a>
    <!-- Student -->
    @role('Student')
    <a href="{{ route('student.my.disciplines') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.my.disciplines')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-cubes fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Мои дисциплины</span>
        </div>
    </a>
    <a href="{{ route('student.my.grades') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.my.grades')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-star fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Моя успеваемость</span>
        </div>
    </a>
    <a href="{{ route('student.my.attendance') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.my.attendance')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-book-open fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Моя посещаемость</span>
        </div>
    </a>
    <a href="{{ route('student.document.order.requests') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.document.order.requests')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-file-circle-plus fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Заказ документов</span>
        </div>
    </a>
    <a href="{{ route('student.academic.retake.and.consultation.schedule') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.academic.retake.and.consultation.schedule')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-calendar-day fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Графики</span>
        </div>
    </a>
    <a href="{{ route('student.spt.consent.status') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.spt.consent.status')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-list-check fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Согласие на СПТ</span>
        </div>
    </a>
    <a href="{{ route('questionnaire') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('questionnaire') || request()->routeIs('student.social.passport')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Анкетирование</span>
        </div>
    </a>
    <a href="{{ route('student.async.interact.chats') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('student.interact') || request()->routeIs('student.interact.view.materials')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-rotate fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Асинхронное взаимодействие</span>
        </div>
    </a>
    @endrole
    <!-- Employee -->
    @can('access-to-admission-campaign')
    <a href="{{ route('admission.campaign') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('admission.campaign')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-school fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Приемная кампания</span>
        </div>
    </a>
    @endcan
    @can('show-operating-activity-and-decisions-rectorate-and-council')
    <a href="{{ route('employee.operating.activity') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('employee.operating.activity')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-list-ol fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Операционная деятельность</span>
        </div>
    </a>
    <a href="{{ route('employee.rectorate.decision') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('employee.rectorate.decision')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-pen-nib fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Решения ректората</span>
        </div>
    </a>
    <a href="{{ route('decisions.council') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('decisions.council')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-feather-pointed fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Решение уч. совета</span>
        </div>
    </a>
    @endcan
    @can('access-to-regulatory-documents')
    <a href="{{ route('employee.regulatory.documents') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('employee.regulatory.documents')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Справочник нормативных документов</span>
        </div>
    </a>
    @endcan
    @can('manage-educational-and-youth-policy-events')
    <a href="{{ route('upbringing.management') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('upbringing.management')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-briefcase fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Управление мероприятиями по ВР и МП</span>
        </div>
    </a>
    @endcan
    @can('show-educational-and-youth-policy-events')
    <a href="{{ route('youth.upbringing.event.info.report') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('youth.upbringing.event.info.report')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-business-time fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Мероприятия по ВР и МП</span>
        </div>
    </a>
    @endcan
    @can('access-to-student-social-passport-review')
    <a href="{{ route('employee.student.social.passport.review') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('employee.student.social.passport.review')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-user-check fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Соц. паспорта на утверждение</span>
        </div>
    </a>
    @endcan
    @can('access-to-contingent-management')
    <a href="{{ route('employee.contingent.management') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('employee.contingent.management')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-users fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Управление контингентом</span>
        </div>
    </a>
    @endcan
    @can('access-to-military')
    <a href="{{ route('military') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('military')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-shield fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Воинский учёт</span>
        </div>
    </a>
    @endcan
    <!-- Teacher -->
    @role('Teacher')
    <a href="{{ route('teacher.digital.profile') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('teacher.digital.profile')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-chalkboard-user fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Цифровой профиль преподавателя</span>
        </div>
    </a>
    @endrole
    <a href="{{ route('reports') }}">
        <div :class="{ 'justify-center': !isMobileMenuVisible, 'space-x-2 px-2': isMobileMenuVisible }" class="h-8 flex items-center {{ (request()->routeIs('reports')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} rounded">
            <div class="w-8 h-8 flex items-center justify-center">
                <i class="fa-solid fa-chart-simple fa-lg"></i>
            </div>
            <span x-show="isMobileMenuVisible" class="md:hidden text-sm whitespace-nowrap">Отчеты и аналитика</span>
        </div>
    </a>
</div>