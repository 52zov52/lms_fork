<div class="flex flex-col space-y-2">
    <button wire:click="add">ADD</button>
    <div class="w-full bg-white rounded shadow-xl p-4">
        <table class="w-auto bg-white border shadow-md text-xs">
            <tbody>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                    <td class="w-auto border px-2 py-2 font-semibold">ФИО</td>
                    <td class="w-32 border px-2 py-2 text-center font-semibold">№ зачетки</td>
                    @foreach($dates as $dateAndTime)
                    <td class="w-32 border px-2 py-2 text-center font-semibold">{{ $dateAndTime->date_lesson }} {{ $dateAndTime->time_lesson }}</td>
                    @endforeach
                    <td class="w-32 border px-2 py-2 text-center font-semibold">{{ $currentData }} {{ $currentTime }}</td>
                </tr>
                @foreach($contingent as $key => $value)
                <tr>
                    <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto border px-2 py-2">{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</td>
                    <td class="w-32 border px-2 py-2 text-center">{{ $value->ticket_number }}</td>
                    @foreach($journal as $item)
                    @if($value->user_id == $item->student_id)
                    <td>
                        <select wire:change="setState({{ $value->user_id }})" class="w-auto border border-gray-300 rounded text-xs font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="1">.</option>
                            <option value="0">Н</option>
                        </select>
                    </td>
                    @endif
                    @endforeach
                    <td class="w-32 border px-2 py-2 text-center">
                        <select wire:change="setState({{ $value->id }})" class="w-auto border border-gray-300 rounded text-xs font-semibold focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">.</option>
                            <option value="1">н</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>