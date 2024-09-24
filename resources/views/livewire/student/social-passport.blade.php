<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button onclick="showToast('save')" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
            <i class="fa-solid fa-floppy-disk fa-lg"></i>
            Сохранить
        </button>
    </div>
    
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-auto px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Критерий</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Значения</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">1</td>
                    <td class="w-auto px-2 py-2 text-left">ФИО</td>
                    <td class="w-auto px-2 py-2 text-left">{{ $lastName }} {{ $firstName }} {{ $middleName }}</td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">2</td>
                    <td class="w-auto px-2 py-2 text-left">Дата рождения<span class="ml-1 text-red-400">*</span></td>
                    <td class="w-auto px-2 py-2 text-left">{{ $birthday }}</td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">3</td>
                    <td class="w-auto px-2 py-2 text-left">Место жительства (по прописке)</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <input wire:model="registeredAddress" type="text" name="registeredAddress" id="registeredAddress" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">4</td>
                    <td class="w-auto px-2 py-2 text-left">Жилищные условия</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <select wire:model="housingConditions" name="housingConditions" id="housingConditions" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Выберите жилищные условия</option>
                            <option value="Частный дом">Частный дом</option>
                            <option value="Квартира">Квартира</option>
                            <option value="Общежитие">Общежитие</option>
                            <option value="Нет жилья">Нет жилья</option>
                        </select>
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">5</td>
                    <td class="w-auto px-2 py-2 text-left">С кем совместно проживаете</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Мать" type="checkbox" class="mr-1">Мать
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Отец" type="checkbox" class="mr-1">Отец
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Бабушка" type="checkbox" class="mr-1">Бабушка
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Дедушка" type="checkbox" class="mr-1">Дедушка
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Опекун" type="checkbox" class="mr-1">Опекун
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Проживаю самостоятельно" type="checkbox" class="mr-1">Проживаю самостоятельно
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="cohabitants" value="Другое" type="checkbox" class="mr-1">Другое
                            </label>
                        </div>
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">6</td>
                    <td class="w-auto px-2 py-2 text-left">Состав семьи</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <select wire:model="familyComposition" name="familyComposition" id="familyComposition" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите состав семьи</option>
                            <option value="Полная семья">Полная семья</option>
                            <option value="Неполная семья">Неполная семья</option>
                        </select>
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">7</td>
                    <td class="w-auto px-2 py-2 text-left">Количество детей в семье</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <input wire:model="numberOfChildren" type="number" name="numberOfChildren" id="numberOfChildren" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">8</td>
                    <td class="w-auto px-2 py-2 text-left">ФИО отца</td>
                    <td class="w-auto flex flex-col px-2 py-2 text-left">
                        <label for="fatherLastName">Фамилия</label>
                        <input wire:model="fatherLastName" type="text" name="fatherLastName" id="fatherLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <label for="fatherFirstName">Имя</label>
                        <input wire:model="fatherFirstName" type="text" name="fatherFirstName" id="fatherFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <label for="fatherMiddleName">Отчество</label>
                        <input wire:model="fatherMiddleName" type="text" name="fatherMiddleName" id="fatherMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">9</td>
                    <td class="w-auto px-2 py-2 text-left">Место работы отца</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <input wire:model="fatherWorkplace" type="text" name="fatherWorkplace" id="fatherWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">10</td>
                    <td class="w-auto px-2 py-2 text-left">Контактный телефон отца</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <input wire:model="fatherPhone" type="text" name="fatherPhone" id="fatherPhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">11</td>
                    <td class="w-auto px-2 py-2 text-left">ФИО матери</td>
                    <td class="w-auto flex flex-col px-2 py-2 text-left">
                        <label for="motherLastName">Фамилия</label>
                        <input wire:model="motherLastName" type="text" name="motherLastName" id="motherLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <label for="motherFirstName">Имя</label>
                        <input wire:model="motherFirstName" type="text" name="motherFirstName" id="motherFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <label for="motherMiddleName">Отчество</label>
                        <input wire:model="motherMiddleName" type="text" name="motherMiddleName" id="motherMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">12</td>
                    <td class="w-auto px-2 py-2 text-left">Место работы матери</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <input wire:model="motherWorkplace" type="text" name="motherWorkplace" id="motherWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">13</td>
                    <td class="w-auto px-2 py-2 text-left">Контактный телефон матери</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <input wire:model="motherPhone" type="text" name="motherPhone" id="motherPhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">14</td>
                    <td class="w-auto px-2 py-2 text-left">Наличие инвалидности у отца</td>
                    <td class="w-auto flex flex-row px-2 py-2 text-left">
                        <div class="flex space-x-1 items-center mr-2">
                            <input wire:model="fatherDisability" type="radio" id="fatherDisabilityYes" value="1">
                            <label for="fatherDisabilityYes" class="cursor-pointer">Да</label>
                        </div>
                        <div class="flex space-x-1 items-center mr-2">
                            <input wire:model="fatherDisability" type="radio" id="fatherDisabilityNo" value="0">
                            <label for="fatherDisabilityNo" class="cursor-pointer">Нет</label>
                        </div>
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">15</td>
                    <td class="w-auto px-2 py-2 text-left">Наличие инвалидности у матери</td>
                    <td class="w-auto flex flex-row px-2 py-2 text-left">
                        <div class="flex space-x-1 items-center mr-2">
                            <input wire:model="motherDisability" wire:change="motherDisability" type="radio" id="motherDisabilityYes" value="1">
                            <label for="motherDisabilityYes" class="cursor-pointer">Да</label>
                        </div>
                        <div class="flex space-x-1 items-center mr-2">
                            <input wire:model="motherDisability" wire:change="motherDisability" type="radio" id="motherDisabilityNo" value="0">
                            <label for="motherDisabilityNo" class="cursor-pointer">Нет</label>
                        </div>
                    </td>
                </tr>
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-center">16</td>
                    <td class="w-auto px-2 py-2 text-left">Относитесь ли вы к одной из перечисленных категорий</td>
                    <td class="w-auto px-2 py-2 text-left">
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="categories" value="Дети-инвалиды" type="checkbox" class="mr-1">Дети-инвалиды
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="categories" value="Сироты" type="checkbox" class="mr-1">Сироты
                            </label>
                        </div>
                        <div class="inline-flex py-2">
                            <label>
                                <input wire:model="categories" value="Дети под опекой" type="checkbox" class="mr-1">Дети под опекой
                            </label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    async function showToast(methodName, param = null) {
        await @this.callMethod(methodName, param);

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }

    function confirmDestroy() {
        if (confirm('Вы уверены, что хотите подтвердить согласие на СПТ?')) {
            showToast('destroy');
        }
    }
</script>
@endpush