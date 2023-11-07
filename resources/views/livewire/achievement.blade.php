<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    <button wire:click="achievementModalVisible">Добавить</button>

    <!-- Modal dialog add achievement -->
    <x-dialog-modal wire:model="isAchievementModalVisible">

        @csrf
        <x-slot name="title">
            Форма добавления индивидуального достижения
        </x-slot>

        <x-slot name="content">
            <div class="w-full">
                <form wire:submit.prevent="addAchievement" enctype="multipart/form-data">
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
                            <input wire:model="file" type="file" name="file" id="" class="w-full px-2">
                            @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="file">Uploading...</div>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="$toggle('isAchievementModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                                {{ __('Закрыть') }}
                            </button>
                            <button wire:loading.attr="disabled" type="submit" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                                {{ __('Загрузить') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </x-slot>

        <x-slot name="footer">
        </x-slot>
    </x-dialog-modal>
</div>