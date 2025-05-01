<div class="flex flex-col space-y-2">
    @can('access-to-report-admission-campaign')
    <h2 class="mt-2 font-semibold">Приемная комиссия:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="printApplicantAverageScore" href="#" class="text-blue-500 hover:underline">Отчет о средних баллах аттестатов абитуриентов</a></li>
        <li><a wire:click="printAdmissionCampaignToDay" href="#" class="text-blue-500 hover:underline">Оперативный отчет о ходе приемной кампании за {{ now()->format('d.m.Y') }}</a></li>
        <li><a wire:click="printAdmissionCampaignFull" href="#" class="text-blue-500 hover:underline">Оперативный отчет о ходе приемной кампании (накопительный)</a></li>
    </ul>
    @endcan
    <h2 class="font-semibold">Расписание:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="teacherModalVisible" href="#" class="text-blue-500 hover:underline">Отчет по преподавателям</a></li>
        <li><a wire:click="groupModalVisible" href="#" class="text-blue-500 hover:underline">Отчет по группе</a></li>
        @role('Teacher')
        <li><a wire:click="roomsModalVisible" href="#" class="text-blue-500 hover:underline">Отчет по загруженности аудиторий</a></li>
        @endrole
    </ul>
    @can('access-to-science')
    <h2 class="mt-2 font-semibold">Наука:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="printCountPublicationByYearsAndStatuses" href="#" class="text-blue-500 hover:underline">Отчет о количестве публикаций в разрезе годов и статусов сборников</a></li>
        <li><a wire:click="printCountUserPublicationsByStatusAndYearRange" href="#" class="text-blue-500 hover:underline">Отчет о количестве публикаций сотрудников в разрезе статусов сборников и годов (текущего, прошлого, за 5 лет)</a></li>
    </ul>
    @endcan
    @can('access-to-contingent')
    <h2 class="mt-2 font-semibold">Контингент:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="generateContingentReport" href="#" class="text-blue-500 hover:underline">Отчет по численности контингента в разрезе уровней и форм обучения</a></li>
        <li><a wire:click="generateContingentByAgeAndGenderReport" href="#" class="text-blue-500 hover:underline">Отчет по численности контингента распределённый по возрасту и полу в разрезе уровней и форм обучения</a></li>
        <li><a wire:click="generateContingentByAgeAndGenderWithoutTeachingFromReport" href="#" class="text-blue-500 hover:underline">Отчет по численности контингента распределённый по возрасту и полу в разрезе уровней обучения</a></li>
        <li><a wire:click="downloadListOfForeignStudents" href="#" class="text-blue-500 hover:underline">Отчет по иностранным обучающимся</a></li>
        <li><a wire:click="showSocialPassportModal" href="#" class="text-blue-500 hover:underline">Социальный паспорт обучающегося</a></li>
    </ul>
    @endcan
    @can('access-to-social-passport-report')
    <h2 class="mt-2 font-semibold">Социальный паспорт обучающегося:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="showSocialPassportModal" href="#" class="text-blue-500 hover:underline">Социальный паспорт обучающегося</a></li>
    </ul>
    @endcan
    @can('access-to-spt')
    <h2 class="mt-2 font-semibold">Социально-психологическое тестирование:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="generateSptReport" href="#" class="text-blue-500 hover:underline">Список студентов с указанием статусов подтверждения согласия на СПТ</a></li>
    </ul>
    @endcan
    @can('access-to-e-journal')
    <h2 class="mt-2 font-semibold">Электронный журнал:</h2>
    <ul class="list-disc px-4">
        <li><a wire:click="showEJournalModal" href="#" class="text-blue-500 hover:underline">Электронный журнал</a></li>
    </ul>
    @endcan

    <!-- Modal dialog schedule by teacher -->
    <x-dialog-modal wire:model="isTeacherModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма выбора критериев отчета
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <select wire:model="fio" name="teacher_id" id="teacher-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите преподавателя</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->teacher }}">{{ $teacher->teacher }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isTeacherModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="generateTeacherReport" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Сформировать отчет') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog schedule by group -->
    <x-dialog-modal wire:model="isGroupModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма выбора критериев отчета
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <select wire:model="group" name="group_id" id="group-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите группу</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->group }}">{{ $group->group }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isGroupModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="generateGroupReport" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Сформировать отчет') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog schedule by rooms -->
    <x-dialog-modal wire:model="isRoomsModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма выбора критериев отчета
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <select wire:model="week" name="week_id" id="week-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите неделю</option>
                        @foreach($weeksRange as $week)
                        <option value="{{ $week['date'] }}">{{ $week['date'] }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isRoomsModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="generateLoadRoomsReport" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Сформировать отчет') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog e-journal -->
    <x-dialog-modal wire:model="isEJournalModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма выбора критериев отчета
            </x-slot>

            <x-slot name="content">
                <div class="w-full flex flex-col space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <select wire:model="selectedAcademicYear" wire:change="setAcademicYear" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите учебный год из списка</option>
                            @foreach($listOfAcademicYear as $academicYear)
                            <option value="{{ $academicYear->academic_year }}">{{ $academicYear->academic_year }}</option>
                            @endforeach
                        </select>
                        <select wire:model="selectedHalfYear" wire:change="setHalfYear" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите полугодие из списка</option>
                            @foreach($listOfHalfYear as $halfYear )
                            <option value="{{ $halfYear }}">{{ $halfYear }}</option>
                            @endforeach
                        </select>
                    </div>
                    <select wire:model="selectedGroup" wire:change="setGroup" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите группу из списка</option>
                        @foreach($listOfGroups as $group)
                        <option value="{{ $group->group_id }}">{{ $group->group_name }}</option>
                        @endforeach
                    </select>
                    <select wire:model="selectedDiscipline" wire:change="setDiscipline" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите дисциплину из списка</option>
                        @foreach($listOfDiscipline as $discipline)
                        <option value="{{ $discipline->work_study_plan_courses_in_semester_id }}">{{ $discipline->name }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isEJournalModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="downloadEJournalReport" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Сформировать отчет') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog social passport -->
    <x-dialog-modal wire:model="isSocialPassportModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма выбора критериев отчета
            </x-slot>

            <x-slot name="content">
                <div class="w-full flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label class="font-semibold">Категория</label>
                        <div class="flex flex-col space-y-2">
                            <div class="mr-2">
                                <label for="child-categories-1" class="flex space-x-1 items-center">
                                    <input wire:model="childCategories" value="Дети-инвалиды" type="checkbox" id="child-categories-1" class="mr-1">Дети-инвалиды
                                </label>
                            </div>
                            <div class="mr-2">
                                <label for="child-categories-2" class="flex space-x-1 items-center">
                                    <input wire:model="childCategories" value="Сироты" type="checkbox" id="child-categories-2" class="mr-1">Сироты
                                </label>
                            </div>
                            <div class="mr-2">
                                <label for="child-categories-3" class="flex space-x-1 items-center">
                                    <input wire:model="childCategories" value="Дети под опекой" type="checkbox" id="child-categories-3" class="mr-1">Дети под опекой
                                </label>
                            </div>
                            <div class="mr-2">
                                <label for="child-categories-4" class="flex space-x-1 items-center">
                                    <input wire:model="childCategories" value="Дети участников СВО" type="checkbox" id="child-categories-4" class="mr-1">Дети участников СВО
                                </label>
                            </div>
                            <div class="mr-2">
                                <label for="child-categories-5" class="flex space-x-1 items-center">
                                    <input wire:model="childCategories" value="Многодетная семья" type="checkbox" id="child-categories-5" class="mr-1">Многодетная семья
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="font-semibold">Наличие Пушкинской карты</label>
                        <div class="flex flex-row space-x-4">
                            <div class="flex space-x-1 items-center">
                                <input wire:model="hasPushkinCard" type="radio" id="hasPushkinCardYes" value="1">
                                <label for="hasPushkinCardYes" class="cursor-pointer">Да</label>
                            </div>
                            <div class="flex space-x-1 items-center">
                                <input wire:model="hasPushkinCard" type="radio" id="hasPushkinCardNo" value="0">
                                <label for="hasPushkinCardNo" class="cursor-pointer">Нет</label>
                            </div>
                            <div class="flex space-x-1 items-center">
                                <input wire:model="hasPushkinCard" type="radio" id="skip-pushkin-card-id" value="2">
                                <label for="skip-pushkin-card-id" class="cursor-pointer">Не учитывать</label>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isSocialPassportModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="downloadSocialPassportReport" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Сформировать отчет') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>