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
        <button wire:click="feedbackModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">Написать обращение</button>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$feedbackMessages->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Тема</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Email</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Сообщение</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Дата и время отправки</td>
                    </tr>
                    @foreach($feedbackMessages as $feedbackMessage)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">{{ $feedbackMessage->subject }}</td>
                        <td class="w-auto border px-2 py-2">{{ $feedbackMessage->email }}</td>
                        <td class="w-auto border px-2 py-2 text-justify">{{ $feedbackMessage->message_text }}</td>
                        <td class="w-auto border px-2 py-2">{{ $feedbackMessage->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Mobile -->
        <div class="block md:hidden flex flex-col space-y-2">
            @foreach($feedbackMessages as $feedbackMessage)
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Тема:</span>
                    <p>{{ $feedbackMessage->subject }}</p>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Email:</span>
                    <p>{{ $feedbackMessage->email }}</p>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Сообщение:</span>
                    <p class="text-justify">{{ $feedbackMessage->message_text }}</p>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата и время отправки:</span>
                    <p>{{ $feedbackMessage->created_at }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует! На текущий момент нет активных обращений.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog for send feedback message -->
    <x-dialog-modal wire:model="isFeedbackModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма обратной связи
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
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
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isFeedbackModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="send" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Отправить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>