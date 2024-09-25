<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-between">
        <div>
            Статус анкеты:
            @if($questionnaireStatus == 0)
            <span class="text-sky-400 font-bold">На редактировании</span>
            @elseif($questionnaireStatus == 1)
            <span class="text-orange-400 font-bold">На утверждении</span>
            @else
            <span class="text-green-400 font-bold">Утверждена</span>
            @endif
        </div>

        @if($questionnaireStatus == 0)
        <button onclick="showToast('save')" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            <i class="fa-solid fa-floppy-disk fa-lg"></i>
            Сохранить
        </button>
        @elseif($questionnaireStatus == 1)
        <button onclick="showToast('backToEdit')" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
            <i class="fa-solid fa-rotate-left fa-lg"></i>
            Вернуть на редактирование
        </button>
        @else
        <button class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            <i class="fa-solid fa-lock fa-lg"></i>
            Редактирование закрыто
        </button>
        @endif
    </div>

    <div class="flex flex-col space-y-2 border rounded border-slate-400/20 p-2">
        <div class="flex flex-col">
            <label class="font-semibold">ФИО</label>
            <span>{{ $lastName }} {{ $firstName }} {{ $middleName }}</span>
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Дата рождения</label>
            <span>{{ $birthday }}</span>
        </div>
        <div class="flex flex-col">
            <label for="registeredAddress" class="font-semibold">Место жительства (по прописке)</label>
            <input wire:model="registeredAddress" type="text" name="registeredAddress" id="registeredAddress" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('registeredAddress') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label for="housingConditions">Жилищные условия</label>
            <select wire:model="housingConditions" name="housingConditions" id="housingConditions" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                <option value="">Выберите жилищные условия</option>
                <option value="Частный дом">Частный дом</option>
                <option value="Квартира">Квартира</option>
                <option value="Общежитие">Общежитие</option>
                <option value="Нет жилья">Нет жилья</option>
            </select>
            @error('housingConditions') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">С кем совместно проживаете</label>
            <div class="flex flex-wrap flex-row lg:space-x-4">
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Мать" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Мать
                    </label>
                </div>
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Отец" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Отец
                    </label>
                </div>
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Бабушка" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Бабушка
                    </label>
                </div>
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Дедушка" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дедушка
                    </label>
                </div>
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Опекун" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Опекун
                    </label>
                </div>
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Проживаю самостоятельно" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Проживаю самостоятельно
                    </label>
                </div>
                <div class="py-2 mr-2">
                    <label>
                        <input wire:model="cohabitants" value="Другое" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Другое
                    </label>
                </div>
            </div>
            @error('cohabitants') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label for="familyComposition" class="font-semibold">Состав семьи</label>
            <select wire:model="familyComposition" name="familyComposition" id="familyComposition" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Выберите состав семьи</option>
                <option value="Полная семья">Полная семья</option>
                <option value="Неполная семья">Неполная семья</option>
            </select>
            @error('familyComposition') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label for="numberOfChildren" class="font-semibold">Количество детей в семье</label>
            <input wire:model="numberOfChildren" type="number" name="numberOfChildren" id="numberOfChildren" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('numberOfChildren') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Отец</label>
            <div class="flex flex-col lg:flex-row lg:space-x-4">
                <div class="flex flex-col">
                    <label for="fatherLastName">Фамилия</label>
                    <input wire:model="fatherLastName" type="text" name="fatherLastName" id="fatherLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    @error('fatherLastName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="fatherFirstName">Имя</label>
                    <input wire:model="fatherFirstName" type="text" name="fatherFirstName" id="fatherFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    @error('fatherFirstName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="fatherMiddleName">Отчество</label>
                    <input wire:model="fatherMiddleName" type="text" name="fatherMiddleName" id="fatherMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    @error('fatherMiddleName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label for="fatherWorkplace" class="font-semibold">Место работы отца</label>
            <input wire:model="fatherWorkplace" type="text" name="fatherWorkplace" id="fatherWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('fatherWorkplace') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label for="fatherPhone" class="font-semibold">Контактный телефон отца</label>
            <input wire:model="fatherPhone" type="text" name="fatherPhone" id="fatherPhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('fatherPhone') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Мать</label>
            <div class="flex flex-col lg:flex-row lg:space-x-4">
                <div class="flex flex-col">
                    <label for="motherLastName">Фамилия</label>
                    <input wire:model="motherLastName" type="text" name="motherLastName" id="motherLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    @error('motherLastName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="motherFirstName">Имя</label>
                    <input wire:model="motherFirstName" type="text" name="motherFirstName" id="motherFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    @error('motherFirstName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="motherMiddleName">Отчество</label>
                    <input wire:model="motherMiddleName" type="text" name="motherMiddleName" id="motherMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    @error('motherMiddleName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label for="motherWorkplace" class="font-semibold">Место работы матери</label>
            <input wire:model="motherWorkplace" type="text" name="motherWorkplace" id="motherWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('motherWorkplace') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label for="motherPhone" class="font-semibold">Контактный телефон матери</label>
            <input wire:model="motherPhone" type="text" name="motherPhone" id="motherPhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('motherPhone') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Наличие инвалидности у отца</label>
            <div class="flex flex-row space-x-4">
                <div class="flex space-x-1 items-center">
                    <input wire:model="fatherDisability" type="radio" id="fatherDisabilityYes" value="1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    <label for="fatherDisabilityYes" class="cursor-pointer">Да</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="fatherDisability" type="radio" id="fatherDisabilityNo" value="0" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    <label for="fatherDisabilityNo" class="cursor-pointer">Нет</label>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Наличие инвалидности у матери</label>
            <div class="flex flex-row space-x-4">
                <div class="flex space-x-1 items-center">
                    <input wire:model="motherDisability" wire:change="motherDisability" type="radio" id="motherDisabilityYes" value="1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    <label for="motherDisabilityYes" class="cursor-pointer">Да</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="motherDisability" wire:change="motherDisability" type="radio" id="motherDisabilityNo" value="0" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    <label for="motherDisabilityNo" class="cursor-pointer">Нет</label>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Относитесь ли вы к одной из перечисленных категорий</label>
            <div class="flex flex-col md:flex-row md:space-x-2">
                <div class="flex space-x-1 items-center">
                    <label>
                        <input wire:model="childCategories" value="Дети-инвалиды" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дети-инвалиды
                    </label>
                </div>
                <div class="flex space-x-1 items-center">
                    <label>
                        <input wire:model="childCategories" value="Сироты" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Сироты
                    </label>
                </div>
                <div class="flex space-x-1 items-center">
                    <label>
                        <input wire:model="childCategories" value="Дети под опекой" type="checkbox" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дети под опекой
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

        if (toast.dataset.notificationType != '') {
            toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

            setTimeout(function() {
                toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
            }, 3500);
        }
    }

    function confirmDestroy() {
        if (confirm('Вы уверены, что хотите подтвердить согласие на СПТ?')) {
            showToast('destroy');
        }
    }
</script>
@endpush