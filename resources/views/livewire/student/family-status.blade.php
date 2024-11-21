<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
            <button wire:click="save" class="w-auto md:w-36 btn-blue">
                <span wire:loading.remove wire:target="save">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Сохранить
                </span>
                <span wire:loading wire:target="save">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </span>
            </button>
        </div>

        <div class="flex flex-col space-y-2 border rounded border-slate-400/20 p-2">
            <div class="flex flex-col">
                <label class="font-semibold">Состоите ли Вы в законном браке на данный момент?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isMarried" wire:change="" type="radio" id="is-married-yes" value="1">
                        <label for="is-married-yes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isMarried" wire:change="" type="radio" id="is-married-no" value="0">
                        <label for="is-married-no" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Если да, то укажите дату заключения брака:</label>
                <input wire:model="marriageDate" type="date" name="marriageDate" id="marriage-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('marriageDate') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Находитесь ли Вы на данный момент в разводе?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isDivorced" wire:change="" type="radio" id="is-divorced-yes" value="1">
                        <label for="is-divorced-yes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isDivorced" wire:change="" type="radio" id="is-divorced-no" value="0">
                        <label for="is-divorced-no" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Если да, то укажите дату развода:</label>
                <input wire:model="divorceDate" type="date" name="divorceDate" id="divorce-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('divorceDate') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Являетесь ли Вы вдовой (вдовцом)?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isWidowed" wire:change="" type="radio" id="isWidowedYes" value="1">
                        <label for="isWidowedYes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isWidowed" wire:change="" type="radio" id="isWidowedNo" value="0">
                        <label for="isWidowedNo" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Состояли ли Вы ранее в браке (укажите периоды нахождения в браке)?</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <div class="flex flex-col">
                        <label for="">Период 1-го брака</label>
                        <div class="flex flex-row space-x-4">
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-1-start">с</label>
                                <input wire:model="previousMarriage1Start" type="date" name="previousMarriage1Start" id="previous-marriage-1-start" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-1-end">по</label>
                                <input wire:model="previousMarriage1End" type="date" name="previousMarriage1End" id="previous-marriage-1-end" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="">Период 2-го брака</label>
                        <div class="flex flex-row space-x-4">
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-2-start">с</label>
                                <input wire:model="previousMarriage2Start" type="date" name="previousMarriage2Start" id="previous-marriage-2-start" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-2-end">по</label>
                                <input wire:model="previousMarriage2End" type="date" name="previousMarriage2End" id="previous-marriage-2-end" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="">Период 3-го брака</label>
                        <div class="flex flex-row space-x-4">
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-3-start">с</label>
                                <input wire:model="previousMarriage3Start" type="date" name="previousMarriage3Start" id="previous-marriage-3-start" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-3-end">по</label>
                                <input wire:model="previousMarriage3End" type="date" name="previousMarriage3End" id="previous-marriage-3-end" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="">Период 4-го брака</label>
                        <div class="flex flex-row space-x-4">
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-4-start">с</label>
                                <input wire:model="previousMarriage4Start" type="date" name="previousMarriage4Start" id="previous-marriage-4-start" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-4-end">по</label>
                                <input wire:model="previousMarriage4End" type="date" name="previousMarriage4End" id="previous-marriage-4-end" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="">Период 5-го брака</label>
                        <div class="flex flex-row space-x-4">
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-5-start">с</label>
                                <input wire:model="previousMarriage5Start" type="date" name="previousMarriage5Start" id="previous-marriage-5-start" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div class="w-1/2 flex flex-col">
                                <label for="previous-marriage-5-end">по</label>
                                <input wire:model="previousMarriage5End" type="date" name="previousMarriage5End" id="previous-marriage-5-end" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Получает ли супруг(а) образование в данный момент?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isSpouseStudying" wire:change="" type="radio" id="isSpouseStudyingYes" value="1">
                        <label for="isSpouseStudyingYes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isSpouseStudying" wire:change="" type="radio" id="isSpouseStudyingNo" value="0">
                        <label for="isSpouseStudyingNo" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Обучается ли супруг(а) в ЧОУ ВО ЮУ (ИУБиП)?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isSpouseInIubip" wire:change="" type="radio" id="isSpouseInIubipYes" value="1">
                        <label for="isSpouseInIubipYes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isSpouseInIubip" wire:change="" type="radio" id="isSpouseInIubipNo" value="0">
                        <label for="isSpouseInIubipNo" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Есть ли у Вас дети?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="hasChildren" wire:change="" type="radio" id="hasChildrenYes" value="1">
                        <label for="hasChildrenYes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="hasChildren" wire:change="" type="radio" id="hasChildrenNo" value="0">
                        <label for="hasChildrenNo" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Если, да, то укажите даты рождения детей:</label>
                <div class="flex flex-col">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="child-1-birth-date">Дата рождения 1-го ребенка</label>
                            <input wire:model="child1BirthDate" type="date" name="child1BirthDate" id="child-1-birth-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex flex-col">
                            <label for="child-2-birth-date">Дата рождения 2-го ребенка</label>
                            <input wire:model="child2BirthDate" type="date" name="child2BirthDate" id="child-2-birth-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex flex-col">
                            <label for="child-3-birth-date">Дата рождения 3-го ребенка</label>
                            <input wire:model="child3BirthDate" type="date" name="child3BirthDate" id="child-3-birth-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex flex-col">
                            <label for="child-4-birth-date">Дата рождения 4-го ребенка</label>
                            <input wire:model="child4BirthDate" type="date" name="child4BirthDate" id="child-4-birth-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div class="flex flex-col">
                            <label for="child-5-birth-date">Дата рождения 5-го ребенка</label>
                            <input wire:model="child5BirthDate" type="date" name="child5BirthDate" id="child-5-birth-date" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold">Имеете ли Вы официальный статус матери (отца) одиночки?</label>
                <div class="flex flex-row space-x-4">
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isSingleParent" wire:change="" type="radio" id="isSingleParentYes" value="1">
                        <label for="isSingleParentYes" class="cursor-pointer">Да</label>
                    </div>
                    <div class="flex space-x-1 items-center">
                        <input wire:model="isSingleParent" wire:change="" type="radio" id="isSingleParentNo" value="0">
                        <label for="isSingleParentNo" class="cursor-pointer">Нет</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>