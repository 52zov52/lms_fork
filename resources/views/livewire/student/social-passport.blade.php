<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button onclick="showToast('save')" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
            <i class="fa-solid fa-floppy-disk fa-lg"></i>
            Сохранить
        </button>
    </div>

    <div class="flex flex-col space-y-2 border rounded border-slate-400/20 p-2">
        <div class="flex flex-col">
            <label>ФИО</label>
            <span>{{ $lastName }} {{ $firstName }} {{ $middleName }}</span>
        </div>
        <div class="flex flex-col">
            <label>Дата рождения</label>
            <span>{{ $birthday }}</span>
        </div>
        <div class="flex flex-col">
            <label for="registeredAddress">Место жительства (по прописке)</label>
            <input wire:model="registeredAddress" type="text" name="registeredAddress" id="registeredAddress" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-col">
            <label for="housingConditions">Жилищные условия</label>
            <select wire:model="housingConditions" name="housingConditions" id="housingConditions" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Выберите жилищные условия</option>
                <option value="Частный дом">Частный дом</option>
                <option value="Квартира">Квартира</option>
                <option value="Общежитие">Общежитие</option>
                <option value="Нет жилья">Нет жилья</option>
            </select>
        </div>
        <div class="flex flex-col">
            <label>С кем совместно проживаете</label>
            <div class="flex flex-row space-x-4">
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
            </div>
        </div>
        <div class="flex flex-col">
            <label for="familyComposition">Состав семьи</label>
            <select wire:model="familyComposition" name="familyComposition" id="familyComposition" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите состав семьи</option>
                <option value="Полная семья">Полная семья</option>
                <option value="Неполная семья">Неполная семья</option>
            </select>
        </div>
        <div class="flex flex-col">
            <label for="numberOfChildren">Количество детей в семье</label>
            <input wire:model="numberOfChildren" type="number" name="numberOfChildren" id="numberOfChildren" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-col">
            <label>Отец</label>
            <div class="flex flex-col lg:flex-row">
                <div class="flex flex-col">
                    <label for="fatherLastName">Фамилия</label>
                    <input wire:model="fatherLastName" type="text" name="fatherLastName" id="fatherLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <label for="fatherFirstName">Имя</label>
                    <input wire:model="fatherFirstName" type="text" name="fatherFirstName" id="fatherFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <label for="fatherMiddleName">Отчество</label>
                    <input wire:model="fatherMiddleName" type="text" name="fatherMiddleName" id="fatherMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label for="fatherWorkplace">Место работы отца</label>
            <input wire:model="fatherWorkplace" type="text" name="fatherWorkplace" id="fatherWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-col">
            <label for="fatherPhone">Контактный телефон отца</label>
            <input wire:model="fatherPhone" type="text" name="fatherPhone" id="fatherPhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-col">
            <label>Мать</label>
            <div class="flex flex-col lg:flex-row">
                <div class="flex flex-col">
                    <label for="motherLastName">Фамилия</label>
                    <input wire:model="motherLastName" type="text" name="motherLastName" id="motherLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <label for="motherFirstName">Имя</label>
                    <input wire:model="motherFirstName" type="text" name="motherFirstName" id="motherFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="flex flex-col">
                    <label for="motherMiddleName">Отчество</label>
                    <input wire:model="motherMiddleName" type="text" name="motherMiddleName" id="motherMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label for="motherWorkplace">Место работы матери</label>
            <input wire:model="motherWorkplace" type="text" name="motherWorkplace" id="motherWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-col">
            <label for="motherPhone">Контактный телефон матери</label>
            <input wire:model="motherPhone" type="text" name="motherPhone" id="motherPhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>
        <div class="flex flex-col space-x-4">
            <label>Наличие инвалидности у отца</label>
            <div class="flex flex-row space-x-4">
                <div class="flex space-x-1 items-center mr-2">
                    <input wire:model="fatherDisability" type="radio" id="fatherDisabilityYes" value="1">
                    <label for="fatherDisabilityYes" class="cursor-pointer">Да</label>
                </div>
                <div class="flex space-x-1 items-center mr-2">
                    <input wire:model="fatherDisability" type="radio" id="fatherDisabilityNo" value="0">
                    <label for="fatherDisabilityNo" class="cursor-pointer">Нет</label>
                </div>
            </div>
        </div>
        <div class="flex flex-col space-x-4">
            <label>Наличие инвалидности у матери</label>
            <div class="flex flex-row space-x-4">
                <div class="flex space-x-1 items-center mr-2">
                    <input wire:model="motherDisability" wire:change="motherDisability" type="radio" id="motherDisabilityYes" value="1">
                    <label for="motherDisabilityYes" class="cursor-pointer">Да</label>
                </div>
                <div class="flex space-x-1 items-center mr-2">
                    <input wire:model="motherDisability" wire:change="motherDisability" type="radio" id="motherDisabilityNo" value="0">
                    <label for="motherDisabilityNo" class="cursor-pointer">Нет</label>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label>Относитесь ли вы к одной из перечисленных категорий</label>
            <div class="flex flex-row space-x-4">
                <div class="flex space-x-1 items-center">
                    <label>
                        <input wire:model="categories" value="Дети-инвалиды" type="checkbox" class="mr-1">Дети-инвалиды
                    </label>
                </div>
                <div class="flex space-x-1 items-center">
                    <label>
                        <input wire:model="categories" value="Сироты" type="checkbox" class="mr-1">Сироты
                    </label>
                </div>
                <div class="flex space-x-1 items-center">
                    <label>
                        <input wire:model="categories" value="Дети под опекой" type="checkbox" class="mr-1">Дети под опекой
                    </label>
                </div>
            </div>
        </div>
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