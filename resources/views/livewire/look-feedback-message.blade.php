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

    @can('send-feedback-responses')
    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <button wire:click="feedbackResponsModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-share"></i>
            Написать ответ на сообщение
        </button>
    </div>
    @endcan

    <div class="flex flex-col space-y-4 bg-white rounded shadow-xl p-4">
        @if(!is_null($feedbackResponse))
        <div class="flex flex-col space-y-4 border border-l-4 border-l-green-500 shadow-xl p-4">
            <div class="flex flex-col">
                <p class="font-semibold">
                    {{ $feedbackResponse->last_name }} {{ $feedbackResponse->first_name }} {{ $feedbackResponse->middle_name }}
            </div>
            <div class="flex flex-col">
                <p class="font-semibold">Ответ на сообщение:</p>
                <p>{{ $feedbackResponse->respons }}</p>
            </div>
            <div class="flex flex-col">
                <p class="font-semibold">Дата и время отправки ответа на сообщения:</p>
                <p class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($feedbackResponse->created_at)->format('d.m.Y H:i:s') }}
                </p>
            </div>
        </div>
        @endif
        <div class="flex flex-col space-y-4 border border-l-4 border-l-blue-500 shadow-xl p-4">
            <div class="flex flex-col">
                <p class="font-semibold">
                    @if($feedbackMessage->sender_id == auth()->user()->id)
                    Вы
                    @else
                    {{ $feedbackMessage->last_name }} {{ $feedbackMessage->first_name }} {{ $feedbackMessage->middle_name }}
                    @endif
                </p>
                <p class="text-sm text-gray-500">{{ $feedbackMessage->email }}</p>
            </div>
            <div class="flex flex-col">
                <p class="font-semibold">Ваше сообщение:</p>
                <p>{{ $feedbackMessage->message_text }}</p>
            </div>
            <div class="flex flex-col">
                <p class="font-semibold">Дата и время отправки сообщения:</p>
                <p class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($feedbackMessage->created_at)->format('d.m.Y H:i:s') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Modal dialog to send feedback respons -->
    <x-dialog-modal wire:model="isFeedbackResponsModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма ответа на сообщение
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <div class="flex flex-col space-y-2">
                        <div class="flex flex-col">
                            <label for="respons-id" class="font-semibold">Ответ на сообщение<span class="ml-1 text-red-500">*</span></label>
                            <textarea wire:model="respons" name="respons" id="respons-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                            @error('respons')<span class="text-red-500 font-semibold">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isFeedbackResponsModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="send" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Отправить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>