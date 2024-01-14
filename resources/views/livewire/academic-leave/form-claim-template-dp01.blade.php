<div class="flex flex-col space-y-2">

    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    @if(session()->has('message-error'))
    <div class="flex items-center bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-error') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-end md:space-x-2 space-y-2 md:space-y-0">
        <button class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-floppy-disk"></i>
            Сохранить
        </button>
        <button class="w-full md:w-auto bg-purple-500 hover:bg-purple-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-check"></i>
            Утвердить
        </button>
        <button class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-print"></i>
            Печать
        </button>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <form class="flex flex-col space-y-2">
            <div class="flex flex-col space-y-2">
                <label for="student-full-name-id" class="font-semibold">ФИО студента</label>
                <input type="text" name="studentFullName" id="student-full-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('studentFullName') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col space-y-2">
                <label for="customer-full-name-id" class="font-semibold">ФИО заказчика (Законного представителя)</label>
                <input type="text" name="customerFullName" id="customer-full-name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('customerFullName') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col space-y-2">
                <span class="font-semibold">Договор</span>
                <div class="grid grid-cols-2 gap-2">
                    <div class="flex flex-col space-y-2">
                        <label for="agreement-date-id" class="font-semibold">От</label>
                        <input type="text" name="agreementDate" id="agreement-date-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('agreementDate') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col space-y-2">
                        <label for="contract-number-id" class="font-semibold">№</label>
                        <input type="text" name="contractNumber" id="contract-number-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('contractNumber') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <label for="payment-amount-id" class="font-semibold">Сумма оплаты</label>
                <input type="text" name="paymentAmount" id="payment-amount-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('paymentAmount') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col space-y-2">
                <span class="font-semibold">Разбить сумму оплаты на</span>
                <div class="felx">
                    <label for="two-stage-id"><input type="radio" name="paymentAmountStage" id="two-stage-id"> 2 этапа</label>
                    <label for="four-stage-id"><input type="radio" name="paymentAmountStage" id="four-stage-id"> 4 этапа</label>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <span class="font-semibold">По состоянию на {{ \Carbon\Carbon::now()->format('Y') }} сумма задолженности</span>
                <label for="debt-amount-id" class="font-semibold"></label>
                <input type="text" name="debtAmount" id="debt-amount-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                @error('debtAmount') <span class="text-red-500">{{ $message }}</span> @enderror
            </div>
        </form>
    </div>

    <div class="flex flex-col md:flex-row justify-end md:space-x-2 space-y-2 md:space-y-0">
        <button class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-floppy-disk"></i>
            Сохранить
        </button>
        <button class="w-full md:w-auto bg-purple-500 hover:bg-purple-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-check"></i>
            Утвердить
        </button>
        <button class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-print"></i>
            Печать
        </button>
    </div>
</div>