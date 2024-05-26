<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col space-y-4">
        <div class="flex flex-col">
            <h2 class="font-semibold">Образовательные уровни</h2>
            <div class="flex flex-row space-x-2 flex-wrap">
                <div class="flex space-x-1 items-center">
                    <input wire:model="levelEducation" wire:change="setLevelEducation(2)" type="radio" id="college" value="2">
                    <label for="college" class="cursor-pointer">СПО</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="levelEducation" wire:change="setLevelEducation(6)" type="radio" id="bachelor" value="6">
                    <label for="bachelor" class="cursor-pointer">Бакалавриат</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="levelEducation" wire:change="setLevelEducation(7)" type="radio" id="master" value="7">
                    <label for="master" class="cursor-pointer">Магистратура</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="levelEducation" wire:change="setLevelEducation(4)" type="radio" id="phd" value="4">
                    <label for="phd" class="cursor-pointer">Аспирантура</label>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <h2 class="font-semibold">Формы обучения</h2>
            <div class="flex flex-row space-x-2 flex-wrap">
                <div class="flex space-x-1 items-center">
                    <input wire:model="teachingForm" wire:change="setTeachingForm(1)" type="radio" id="full-time-form" value="1">
                    <label for="full-time-form" class="cursor-pointer">Очная</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="teachingForm" wire:change="setTeachingForm(6)" type="radio" id="part-time-form" value="6">
                    <label for="part-time-form" class="cursor-pointer">Очно-заочная</label>
                </div>
                <div class="flex space-x-1 items-center">
                    <input wire:model="teachingForm" wire:change="setTeachingForm(2)" type="radio" id="distance-time-form" value="2">
                    <label for="distance-time-form" class="cursor-pointer">Заочная</label>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button onclick="showToast('update')" class="bg-blue-400 hover:bg-blue-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 hover:border-blue-500 dark:border-slate-400/20 px-2 py-2 text-xs text-left text-white dark:text-blue-400 font-bold">
            <i class="fa-solid fa-floppy-disk fa-lg"></i>
            Сохранить
        </button>
    </div>
    <table class="w-full text-xs lg:text-sm">
        <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
            <tr class="border-b border-slate-400/20">
                <th scope="col" rowspan="2" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                <th scope="col" rowspan="2" class="w-auto px-2 py-2 text-left font-semibold">Направление подготовки</th>
                <th scope="col" rowspan="2" class="w-32 px-2 py-2 text-left font-semibold">Тип финансирования</th>
                <th scope="col" colspan="3" class="w-auto px-2 py-2 text-center font-semibold">Заявления</th>
                <th scope="col" colspan="3" class="w-auto px-2 py-2 text-center font-semibold">Договора</th>
                <th scope="col" rowspan="2" class="w-24 px-2 py-2 text-center font-semibold">Оплачено</th>
            </tr>
            <tr class="border-b border-slate-400/20">
                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">На базе аттестата</th>
                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Диплом ЮУ</th>
                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Диплом СПО</th>
                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">На базе аттестата</th>
                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Диплом ЮУ</th>
                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Диплом СПО</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $value)
            <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                <td class="w-auto px-2 py-2">
                    {{ $value->speciality_code }} {{ $value->speciality_name }} @if(!is_null($value->speciality_profile)) ({{ $value->speciality_profile }}) @endif
                </td>
                <td class="w-32 px-2 py-2">
                    @if($value->funding_type == 0)
                        Бюджет
                    @else
                        Коммерция
                    @endif
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="applicationsCertificateCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="applicationsDiplomaBachelorCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="applicationsDiplomaCollegeCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="contractsCertificateCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="contractsDiplomaBachelorCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="contractsDiplomaCollegeCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
                <td class="w-20 px-2 py-2">
                    <input wire:model="paidContractsCount.{{ $value->id }}" wire:key="{{ $value->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    async function showToast(methodName, param = null) {
        await @this.callMethod(methodName, param);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }
</script>
@endpush