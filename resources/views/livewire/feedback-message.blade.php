<div class="flex flex-col space-y-2">

    @if(session()->has('message-success') && $isAlertVisible)
    <div class="flex items-center justify-between bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                {{ session('message-success') }}
            </div>
        </div>
        <button wire:click="alertVisible"><i class="fa-solid fa-xmark fal-lg"></i></button>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <button wire:click="feedbackMessageModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-paper-plane"></i>
            Написать сообщение
        </button>
    </div>

    <div class="flex flex-col space-y-2 bg-white rounded shadow-xl p-4">
        @if(!$feedbackMessages->isEmpty())
        <table class="w-full bg-white border shadow-md text-sm">
            <tbody>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                    <td class="w-auto border px-2 py-2 font-semibold">Тема сообщения</td>
                    <td class="w-24 border px-2 py-2 text-center font-semibold">Действия</td>
                </tr>
                @foreach($feedbackMessages as $feedbackMessage)
                <tr>
                    <td class="w-12 border px-2 py-2 text-center font-semibold">{{ $loop->iteration }}</td>
                    <td class="w-auto border px-2 py-2 font-semibold">
                        @if($feedbackMessage->is_processed == 1)
                        <i class="fa-solid fa-envelope"></i>
                        @else
                        <i class="fa-solid fa-envelope-open"></i>
                        @endif
                        {{ $feedbackMessage->subject }}
                    </td>
                    <td class="w-24 border px-2 py-2 text-center font-semibold">
                        <button wire:click="redirectToLookFeedbackMessage({{ $feedbackMessage->id }})" class="bg-purple-500 hover:bg-purple-600 rounded shadow-xl px-2 py-1.5 text-xs text-white" title="Просмотр сообщений">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-2">
            {{ $feedbackMessages->links() }}
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует! На текущий момент у Вас нет активных сообщений.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog to send feedback message -->
    <x-dialog-modal wire:model="isFeedbackMessageModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма обратной связи
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <div class="flex flex-col space-y-2">
                        <div class="flex flex-col">
                            <label for="subject-id" class="font-semibold">Тема сообщения<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="subject" type="subject" name="subject" id="subject-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('subject')<span class="text-red-500 font-semibold">{{ $message }}</span>@enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="email-id" class="font-semibold">Ваш email<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="email" type="email" name="email" id="email-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('email')<span class="text-red-500 font-semibold">{{ $message }}</span>@enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="message-text-id" class="font-semibold">Сообщение<span class="ml-1 text-red-500">*</span></label>
                            <textarea wire:model="messageText" name="messageText" id="message-text-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                            @error('messageText')<span class="text-red-500 font-semibold">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isFeedbackMessageModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="send" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Отправить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>