<div class="flex flex-col space-y-2">

    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    @if(session()->has('message-error'))
    <div class="flex items-center bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-error') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-end md:space-x-2 space-y-2 md:space-y-0">
        <button class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-floppy-disk"></i>
            Сохранить
        </button>
        <button class="w-full md:w-auto bg-purple-500 hover:bg-purple-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-check"></i>
            Утвердить
        </button>
        <button class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-print"></i>
            Печать
        </button>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <form class="flex flex-col space-y-2">
            <div class="grid grid-cols-2 gap-2">
                <div class="flex flex-col space-y-2">
                    <label for="student-full-name-id" class="font-semibold">ФИО студента (ИМ падеж)</label>
                    <input type="text" name="studentFullName" id="student-full-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('studentFullName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="student-full-name-dative-id" class="font-semibold">ФИО студента (ДТ падеж)</label>
                    <input type="text" name="studentFullNameDative" id="student-full-name-dative-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('studentFullNameDative') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="flex flex-col space-y-2">
                    <label for="group-id" class="font-semibold">Группа</label>
                    <input type="text" name="group" id="group-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('group') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="course-id" class="font-semibold">Курс</label>
                    <input type="text" name="course" id="course-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('course') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="teaching-form-name-id" class="font-semibold">Форма обучения</label>
                    <input type="text" name="teachingFormName" id="teaching-form-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('teachingFormName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="speciality-id" class="font-semibold">Направление подготовки/Специальность</label>
                <input type="text" name="speciality" id="speciality-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('speciality') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col space-y-2">
                <span class="font-semibold">Период академ. отпуска</span>
                <div class="grid grid-cols-2 gap-2">
                    <div class="w-full flex flex-col">
                        <label for="start-date-id" class="font-semibold">с</label>
                        <input wire:model="startDate" type="text" id="start-date-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" tabindex="-1">
                        @error('startDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="w-full flex flex-col">
                        <label for="end-date-id" class="font-semibold">по</label>
                        <input wire:model="endDate" type="text" id="end-date-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" tabindex="-1">
                        @error('endDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="flex flex-col md:flex-row justify-end md:space-x-2 space-y-2 md:space-y-0">
        <button class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-floppy-disk"></i>
            Сохранить
        </button>
        <button class="w-full md:w-auto bg-purple-500 hover:bg-purple-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-check"></i>
            Утвердить
        </button>
        <button class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-print"></i>
            Печать
        </button>
    </div>
</div>

@push('scripts')
<script>
    var pickerStartDate = new Pikaday({
        field: document.getElementById('start-date-id'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            var data = this.getMoment().format('DD.MM.YYYY');
            @this.set('startDate', data);
        }
    });

    var pickerEndDate = new Pikaday({
        field: document.getElementById('end-date-id'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            var data = this.getMoment().format('DD.MM.YYYY');
            @this.set('endDate', data);
        }
    });
</script>
@endpush