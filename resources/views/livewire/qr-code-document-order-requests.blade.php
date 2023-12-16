<div class="flex flex-col space-y-2 w-full bg-white border border-t-4 border-t-blue-500 rounded-md shadow-xl p-6">
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
    <h2 class="text-center text-lg font-semibold">Заказ документа(справки) по QR-коду</h2>
    <div>
        <select wire:model="selectedDocumentTypeId" wire:change="setDocumentTypeId" name="document_type_id" id="document-type-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка документ</option>
            <option value="1">Справка о статусе студента</option>
            <option value="2">Справка о статусе студента (для транспортных карт) - только для студентов колледжа</option>
        </select>
    </div>
    <div>
        <select wire:model="selectedGroupId" wire:change="setGroupId" name="group_id" id="group-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите группу из списка</option>
            @foreach($groups as $group)
            <option value="{{ $group->id }}">{{ $group->group_name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <select wire:model="selectedUserId" wire:change="setUserId" name="user_id" id="user-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите обучающегося из списка</option>
            @foreach($students as $student)
            <option value="{{ $student->user_id }}">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mt-4">
        <button wire:click="orderDocument" type="button" class="w-full block bg-blue-500 hover:bg-blue-600 rounded px-4 py-2 text-white font-semibold">Заказать справку</button>
    </div>
</div>