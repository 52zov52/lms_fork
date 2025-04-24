<div class="flex flex-col space-y-2">

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-between">
        <div>
            Статус анкеты:
            @if($questionnaireStatus == 0)
            <span class="text-sky-400 font-bold">На редактировании</span>
            @elseif($questionnaireStatus == 1)
            <span class="text-orange-400 font-bold">На подтверждении</span>
            @else
            <span class="text-green-400 font-bold">Подтверждена</span>
            @endif
        </div>

        @if($questionnaireStatus == 0)
        <button wire:click="save" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            <i class="fa-solid fa-floppy-disk fa-lg"></i>
            Сохранить
        </button>
        @elseif($questionnaireStatus == 1)
        <button wire:click="backToEdit" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
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

    <div x-data="{ isMarrieted: {{ $maritalStatus ? 'true' : 'false' }} }" class="flex flex-col space-y-2 border rounded border-slate-400/20 p-2">
        <div class="flex flex-col">
            <label class="font-semibold">ФИО</label>
            <span>{{ $lastName }} {{ $firstName }} {{ $middleName }}</span>
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Дата рождения</label>
            <span>{{ $birthday }}</span>
        </div>
        <div class="flex flex-col">
            <label for="citizenship" class="font-semibold">Гражданство</label>
            <input wire:model="citizenship" type="text" name="citizenship" id="citizenship" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
            @error('citizenship') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label for="registeredAddress" class="font-semibold">Место жительства (по прописке)</label>
            <input wire:model="registeredAddress" type="text" name="registeredAddress" id="registeredAddress" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Укажите место жительства в формате: Страна, регион, населенный пункт, улица, дом, корпус и № квартиры (если есть)" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
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
            <div class="flex flex-col md:flex-row md:space-x-4">
                <div class="mr-2">
                    <label for="cohabitants-1" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Мать" type="checkbox" id="cohabitants-1" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Мать
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-2" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Отец" type="checkbox" id="cohabitants-2" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Отец
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-3" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Бабушка" type="checkbox" id="cohabitants-3" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Бабушка
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-4" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Дедушка" type="checkbox" id="cohabitants-4" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дедушка
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-5" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Опекун" type="checkbox" id="cohabitants-5" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Опекун
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-6" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Проживаю самостоятельно" type="checkbox" id="cohabitants-6" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Проживаю самостоятельно
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-7" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Супруг/супруга" type="checkbox" id="cohabitants-7" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Супруг/супруга
                    </label>
                </div>
                <div class="mr-2">
                    <label for="cohabitants-8" class="flex space-x-1 items-center">
                        <input wire:model="cohabitants" value="Другое" type="checkbox" id="cohabitants-8" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Другое
                    </label>
                </div>
            </div>
            @error('cohabitants') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col">
            <label class="font-semibold">Семейное положение</label>
            <div class="flex flex-row space-x-4">
                <div class="flex space-x-1 items-center">
                    <input x-model="isMarrieted" wire:model="maritalStatus" type="radio" id="maritalStatusYes" value="1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    <label for="maritalStatusYes" class="cursor-pointer">В браке</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input x-model="isMarrieted" wire:model="maritalStatus" type="radio" id="maritalStatusNo" value="0" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                    <label for="maritalStatusNo" class="cursor-pointer">Не в браке</label>
                </div>
            </div>
        </div>
        <div x-show="isMarrieted == true">
            <div class="flex flex-col">
                <label class="font-semibold">Супруг/супруга</label>
                <div class="flex flex-col lg:flex-row lg:space-x-4">
                    <div class="flex flex-col">
                        <label for="spouseLastName">Фамилия</label>
                        <input wire:model="spouseLastName" type="text" name="spouseLastName" id="spouseLastName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                        @error('spouseLastName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="spouseFirstName">Имя</label>
                        <input wire:model="spouseFirstName" type="text" name="spouseFirstName" id="spouseFirstName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                        @error('spouseFirstName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="spouseMiddleName">Отчество</label>
                        <input wire:model="spouseMiddleName" type="text" name="spouseMiddleName" id="spouseMiddleName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                        @error('spouseMiddleName') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label for="spouseWorkplace" class="font-semibold">Место работы супруга/супруги</label>
                <input wire:model="spouseWorkplace" type="text" name="spouseWorkplace" id="spouseWorkplace" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                @error('spouseWorkplace') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col">
                <label for="spousePhone" class="font-semibold">Контактный телефон супруга/супруги</label>
                <input wire:model="spousePhone" type="text" name="spousePhone" id="spousePhone" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
                @error('spousePhone') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex flex-col">
            <label for="familyComposition" class="font-semibold">Состав семьи</label>
            <select wire:model="familyComposition" name="familyComposition" id="familyComposition" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>
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
            <div class="flex flex-col md:flex-row md:space-x-4">
                <div class="mr-2">
                    <label for="child-categories-1" class="flex space-x-1 items-center">
                        <input wire:model="childCategories" value="Дети-инвалиды" type="checkbox" id="child-categories-1" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дети-инвалиды
                    </label>
                </div>
                <div class="mr-2">
                    <label for="child-categories-2" class="flex space-x-1 items-center">
                        <input wire:model="childCategories" value="Сироты" type="checkbox" id="child-categories-2" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Сироты
                    </label>
                </div>
                <div class="mr-2">
                    <label for="child-categories-3" class="flex space-x-1 items-center">
                        <input wire:model="childCategories" value="Дети под опекой" type="checkbox" id="child-categories-3" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дети под опекой
                    </label>
                </div>
                <div class="mr-2">
                    <label for="child-categories-4" class="flex space-x-1 items-center">
                        <input wire:model="childCategories" value="Дети участников СВО" type="checkbox" id="child-categories-4" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Дети участников СВО
                    </label>
                </div>
                <div class="mr-2">
                    <label for="child-categories-5" class="flex space-x-1 items-center">
                        <input wire:model="childCategories" value="Многодетная семья" type="checkbox" id="child-categories-5" class="mr-1" @if($questionnaireStatus==1 || $questionnaireStatus==2) disabled @endif>Многодетная семья
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>