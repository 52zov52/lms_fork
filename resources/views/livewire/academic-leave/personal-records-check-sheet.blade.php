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
            <div class="grid grid-cols-3 gap-2">
                <div class="flex flex-col space-y-2">
                    <label for="date-of-inspection-id" class="font-semibold">Дата проверки</label>
                    <input type="text" name="dateOfInspection" id="date-of-inspection-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('dateOfInspection') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="full-name-inspector-id" class="font-semibold">ФИО проверяющего</label>
                    <input type="text" name="fullNameInspector" id="full-name-inspector-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('fullNameInspector') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="reason-for-checking-id" class="font-semibold">Причина проверки</label>
                    <select name="reasonForChecking" id="reason-for-checking-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите причину для проверки</option>
                    </select>
                    @error('reasonForChecking') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="flex flex-col space-y-2">
                    <label for="student-full-name-id" class="font-semibold">ФИО студента (ИМ падеж)</label>
                    <input type="text" name="studentFullName" id="student-full-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('studentFullName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="group-id" class="font-semibold">Группа</label>
                    <input type="text" name="group" id="group-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('group') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col space-y-2">
                    <label for="speciality-id" class="font-semibold">Направление подготовки/Специальность</label>
                    <input type="text" name="speciality" id="speciality-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('speciality') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <table class="w-full bg-white border shadow-md text-xs">
                    <tbody>
                        <tr class="bg-gray-100">
                            <td class="w-14 border px-2 py-2 text-center font-semibold">№ пп</td>
                            <td class="w-auto border px-2 py-2 font-semibold">Документ</td>
                            <td class="w-24 border px-2 py-2 text-center font-semibold">Статус</td>
                        </tr>
                        @foreach($listDocumentsVerification as $key => $value)
                        <tr>
                            <td class="w-14 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto border px-2 py-2">{{ $key }}</td>
                            <td class="w-24 border px-2 py-2 text-center">
                                <input type="checkbox" name="{{ $value }}" id="">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col space-y-2">
                <span class="font-semibold">Для иностранных граждан (дополнительно)</span>
                <table class="w-full bg-white border shadow-md text-xs">
                    <tbody>
                        <tr class="bg-gray-100">
                            <td class="w-14 border px-2 py-2 text-center font-semibold">№ пп</td>
                            <td class="w-auto border px-2 py-2 font-semibold">Документ</td>
                            <td class="w-24 border px-2 py-2 text-center font-semibold">Статус</td>
                        </tr>
                        @foreach($listDocumentsVerificationForeigner as $key => $value)
                        <tr>
                            <td class="w-14 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto border px-2 py-2">{{ $key }}</td>
                            <td class="w-24 border px-2 py-2 text-center">
                                <input type="checkbox" name="{{ $value }}" id="">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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