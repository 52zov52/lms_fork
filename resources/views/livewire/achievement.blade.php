<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <button wire:click="achievementModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">Добавить индивидуальное достижение</button>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$achievements->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Категория</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Наименование</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Описание</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Год</td>
                        <td class="w-auto border px-2 py-2 text-center font-semibold">Файл</td>
                    </tr>
                    @foreach($achievements as $item)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">
                            @if($item->achievement_category == 1)
                            {{ __('Образование и профессиональное развитие') }}
                            @elseif($item->achievement_category == 2)
                            {{ __('Научно-исследовательская деятельность') }}
                            @elseif($item->achievement_category == 3)
                            {{ __('Учебно-методическая деятельность') }}
                            @elseif($item->achievement_category == 4)
                            {{ __('Общественная и творческая деятельность') }}
                            @elseif($item->achievement_category == 5)
                            {{ __('Профессиональные достижения и проекты') }}
                            @else
                            {{ __('Прочее') }}
                            @endif
                        </td>
                        <td class="w-auto border px-2 py-2">{{ $item->name }}</td>
                        <td class="w-auto border px-2 py-2">{{ $item->description }}</td>
                        <td class="w-auto border px-2 py-2 text-center">{{ $item->year }}</td>
                        <td class="w-auto border px-2 py-2 text-center">
                            <a href="storage/{{ $item->file_path }}" target="_blank" rel="noopener noreferrer">Скачать</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Mobile -->
        <div class="block md:hidden flex flex-col space-y-2">
            @foreach($achievements as $item)
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Категория:</span>
                    @if($item->achievement_category == 1)
                    <span>{{ __('Образование и профессиональное развитие') }}</span>
                    @elseif($item->achievement_category == 2)
                    <span>{{ __('Научно-исследовательская деятельность') }}</span>
                    @elseif($item->achievement_category == 3)
                    <span>{{ __('Учебно-методическая деятельность') }}</span>
                    @elseif($item->achievement_category == 4)
                    <span>{{ __('Общественная и творческая деятельность') }}</span>
                    @elseif($item->achievement_category == 5)
                    <span>{{ __('Профессиональные достижения и проекты') }}</span>
                    @else
                    <span>{{ __('Прочее') }}</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Наименование:</span>
                    <span>{{ $item->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Описание:</span>
                    <span>{{ $item->description }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Год:</span>
                    <span>{{ $item->year }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Файл:</span>
                    <a href="storage/{{ $item->file_path }}" target="_blank" rel="noopener noreferrer">Скачать</a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует! Вы еще не загружали индивидуальные достижения.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog for add achievement -->
    <x-dialog-modal wire:model="isAchievementModalVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                Форма добавления индивидуального достижения
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <div class="flex flex-col space-y-2">
                        <div class="flex flex-col">
                            <label for="achievement-category-id"></label>
                            <select wire:model="achievementCategory" name="achievementCategory" id="achievement-category-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите категорию</option>
                                @foreach($achievementCategories as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('achievementCategory') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="name-id">Наименование</label>
                            <input wire:model="name" type="text" name="name" id="name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="description-id">Описание</label>
                            <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="flex flex-col">
                            <label for="year-id">Год</label>
                            <input wire:model="year" type="text" name="year" id="year-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('year') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <input wire:model="file" type="file" name="file" id="file-id" class="w-full">
                            @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="file">Идет загрузка файла ...</div>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isAchievementModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="addAchievement" wire:loading.attr="disabled" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Загрузить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>