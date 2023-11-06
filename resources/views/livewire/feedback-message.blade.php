<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    <div class="bg-white rounded shadow-xl p-4">
        <div class="flex flex-col space-y-2">
            <div class="flex flex-col">
                <label for="email-id">Ваш email:</label>
                <input wire:model="email" type="email" name="email" id="email-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('email')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>
            <div class="flex flex-col">
                <label for="message-text-id">Сообщение:</label>
                <textarea wire:model="messageText" name="messageText" id="message-text-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                @error('messageText')<span class="text-red-500">{{ $message }}</span>@enderror
            </div>
            <div class="flex justify-end">
                <button wire:click="send" type="button" class="w-32 bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Отправить') }}
                </button>
            </div>
        </div>
    </div>
</div>