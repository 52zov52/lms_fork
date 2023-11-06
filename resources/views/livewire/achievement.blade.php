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
                    <input wire:model="achievement" type="file" name="achievement" id="">
                    @error('achievement') <span class="text-red-500">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="achievement">Uploading...</div>
                    <button wire:click="$toggle('isAchievementModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                        {{ __('Закрыть') }}
                    </button>
                    <button wire:loading.attr="disabled" type="submit" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                        {{ __('Загрузить') }}
                    </button>
                </form>
            </div>
        </x-slot>

        <x-slot name="footer">
        </x-slot>
    </x-dialog-modal>
</div>