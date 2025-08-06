<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-between">
            <div class="text-sm">
                Статус анкеты:
                @if(is_null($status))
                <span class="text-sky-400 font-bold">На редактировании</span>
                @elseif($status == 1)
                <span class="text-green-400 font-bold">Сохранена {{ $createdAt }}</span>
                @else
                <span class="text-green-400 font-bold">Обновлена {{ $updatedAt }}</span>
                @endif
            </div>
            <button wire:click="action" class="w-auto md:w-36 btn-add">
                <span wire:loading.remove wire:target="action">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Сохранить
                </span>
                <span wire:loading wire:target="action">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </span>
            </button>
        </div>

        <div class="flex flex-col space-y-2 border rounded border-slate-400/20 p-2 text-xs md:text-sm">
            <div class="flex flex-col">
                <label for="militaryDocument" class="font-semibold">Документ воинского учета</label>
                <select wire:model="militaryDocument" name="militaryDocument" id="militaryDocument" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">Выберите из списка тип документа</option>
                    <option value="Удостоверение гражданина, подлежащего призыву на военную службу">Приписное удостоверение</option>
                    <option value="Военный билет">Военный билет</option>
                </select>
                @error('militaryDocument') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="flex flex-col">
                    <label for="documentSeries" class="font-semibold">Серия документа</label>
                    <input wire:model="documentSeries" type="text" name="documentSeries" id="documentSeries" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('documentSeries') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="documentNumber" class="font-semibold">Номер документа</label>
                    <input wire:model="documentNumber" type="text" name="documentNumber" id="documentNumber" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('documentNumber') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="flex flex-col">
                    <label for="issuedBy" class="font-semibold">Кем выдан документ</label>
                    <input wire:model="issuedBy" type="text" name="issuedBy" id="issuedBy" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('issuedBy') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="issueDate" class="font-semibold">Дата выдачи документу</label>
                    <input wire:model="issueDate" type="date" name="issueDate" id="issueDate" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('issueDate') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="flex flex-col">
                    <label for="militaryCommissariatName" class="font-semibold">Наименование военного комиссариата по месту воинского учета</label>
                    <input wire:model="militaryCommissariatName" type="text" name="militaryCommissariatName" id="militaryCommissariatName" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('militaryCommissariatName') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="fitnessCategory" class="font-semibold">Категория годности к военной службе</label>
                    <select wire:model="fitnessCategory" name="fitnessCategory" id="fitnessCategory" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Выберите из списка категорию годности</option>
                        <option value="А">А</option>
                        <option value="Б">Б</option>
                        <option value="В">В</option>
                        <option value="Г">Г</option>
                        <option value="Д">Д</option>
                    </select>
                    @error('fitnessCategory') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="flex flex-col">
                    <label for="reserveCategory" class="font-semibold">Категория запаса *</label>
                    <input wire:model="reserveCategory" type="text" name="reserveCategory" id="reserveCategory" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('reserveCategory') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="militaryRank" class="font-semibold">Воинское звание *</label>
                    <input wire:model="militaryRank" type="text" name="militaryRank" id="militaryRank" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('militaryRank') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="militaryOccupationProfile" class="font-semibold">Состав (профиль) *</label>
                    <input wire:model="militaryOccupationProfile" type="text" name="militaryOccupationProfile" id="militaryOccupationProfile" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('militaryOccupationProfile') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="fullMilitarySpecialtyCode" class="font-semibold">Полное кодовое обозначение ВУС *</label>
                    <input wire:model="fullMilitarySpecialtyCode" type="text" name="fullMilitarySpecialtyCode" id="fullMilitarySpecialtyCode" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('fullMilitarySpecialtyCode') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <label class="font-semibold">Состоите на воинском учете:</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="flex flex-col">
                    <label for="militaryRegistrationTypeGeneral" class="font-semibold">Общем</label>
                    <input wire:model="militaryRegistrationTypeGeneral" type="text" name="militaryRegistrationTypeGeneral" id="militaryRegistrationTypeGeneral" placeholder="Номер команды, партии" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('militaryRegistrationTypeGeneral') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col">
                    <label for="militaryRegistrationTypeSpecial" class="font-semibold">Специальном</label>
                    <input wire:model="militaryRegistrationTypeSpecial" type="text" name="militaryRegistrationTypeSpecial" id="militaryRegistrationTypeSpecial" placeholder="Номер удостоверения об отсрочке или именного списка о зачислении на спецучет" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('militaryRegistrationTypeSpecial') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <p class="text-xs md:text-sm font-semibold">* Заполняется только для типа документа: военный билет.</p>
    </div>
</div>