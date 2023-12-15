<div class="flex flex-col space-y-2">
    <div class="bg-white rounded shadow-xl p-4">
        <h2 class="font-semibold">Расписание:</h2>
        <ul class="list-disc px-4">
            <li><a wire:click="teacherModalVisible" href="#" class="hover:font-semibold hover:underline">Отчет по преподавателям</a></li>
            <li><a wire:click="groupModalVisible" href="#" class="hover:font-semibold hover:underline">Отчет по группе</a></li>
            @role('Teacher')
            <li><a wire:click="roomsModalVisible" href="#" class="hover:font-semibold hover:underline">Отчет по загруженности аудиторий</a></li>
            @endrole
        </ul>
        <h2 class="mt-2 font-semibold">Контингент:</h2>
        <ul class="list-disc px-4">
            <li><a wire:click="generateContingentReport" href="#"  class="hover:font-semibold hover:underline">Отчет по контингенту</a></li>
        </ul>
    </div>

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
</div>