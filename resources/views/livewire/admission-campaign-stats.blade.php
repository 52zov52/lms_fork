<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col space-y-4">
        <div class="flex flex-col">
            <h2 class="font-semibold">Образовательные уровни</h2>
            <div class="flex flex-row flex-wrap">
                <div class="flex space-x-1 items-center mr-2">
                    <input wire:model="levelEducation" wire:change="setLevelEducation(2)" type="radio" id="college" value="2">
                    <label for="college" class="cursor-pointer">СПО</label>
                </div>
                <div class="flex space-x-1 items-center mr-2">
                    <input wire:model="levelEducation" wire:change="setLevelEducation(6)" type="radio" id="bachelor" value="6">
                    <label for="bachelor" class="cursor-pointer">Бакалавриат</label>
                </div>
                <div class="flex space-x-1 items-center mr-2">
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
        <button onclick="showToast('update')" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
            <i class="fa-solid fa-floppy-disk fa-lg"></i>
            Сохранить
        </button>
        <button wire:click="printFullReport()" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-left text-green-400 hover:text-white font-bold">
            <i class="fa-solid fa-file-excel"></i>
            Скачать отчет
        </button>
    </div>

    @if(count($college) != 0)
    <div class="w-admission-campaign h-admission-campaign overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <table class="w-full text-xs lg:text-sm">
            <thead class="z-10 sticky top-0 bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" rowspan="2" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" rowspan="2" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">Направление подготовки</th>
                    <th scope="col" rowspan="2" class="w-16 px-2 py-2 font-semibold">Основа</th>
                    <th scope="col" colspan="2" class="w-auto px-2 py-2 text-center font-semibold">Заявления</th>
                    <th scope="col" colspan="2" class="w-auto px-2 py-2 text-center font-semibold">Договора</th>
                    <th scope="col" rowspan="2" class="w-24 px-2 py-2 text-center font-semibold">Оплачено</th>
                </tr>
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">после 9 класса</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">после 11 класса</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">после 9 класса</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">после 11 класса</th>
                </tr>
            </thead>
            <tbody>
                @foreach($college as $value_college)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $value_college->id }}</td>
                    <td class="z-0 sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">
                        {{ $value_college->speciality_code }} {{ $value_college->speciality_name }} @if(!is_null($value_college->speciality_profile)) ({{ $value_college->speciality_profile }}) @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value_college->funding_type == 0)
                        <span class="font-bold cursor-pointer">Б</span>
                        @else
                        <span class="font-bold cursor-pointer">К</span>
                        @endif
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="collegeApplicationsAfterNineGradesCount.{{ $value_college->id }}" wire:key="{{ $value_college->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="collegeApplicationsAfterEleventhGradesCount.{{ $value_college->id }}" wire:key="{{ $value_college->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="collegeContractsAfterNineGradesCount.{{ $value_college->id }}" wire:key="{{ $value_college->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="collegeContractsAfterEleventhGradesCount.{{ $value_college->id }}" wire:key="{{ $value_college->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="collegePaidContractsCount.{{ $value_college->id }}" wire:key="{{ $value_college->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(count($college) == 0 && $levelEducation == 2)
    <x-alert type="info" title="Информация" message="Для выбранной формы обучения приемная кампания не проводится" />
    @endif

    @if(count($bachelor) != 0)
    <div class="w-admission-campaign h-admission-campaign overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <table class="w-full text-xs lg:text-sm">
            <thead class="z-10 sticky top-0 bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" rowspan="2" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" rowspan="2" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">Направление подготовки</th>
                    <th scope="col" rowspan="2" class="w-16 px-2 py-2 text-center font-semibold">Основа</th>
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
                @foreach($bachelor as $value_bachelor)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $value_bachelor->id }}</td>
                    <td class="z-0 sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">
                        {{ $value_bachelor->speciality_code }} {{ $value_bachelor->speciality_name }} @if(!is_null($value_bachelor->speciality_profile)) ({{ $value_bachelor->speciality_profile }}) @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value_bachelor->funding_type == 0)
                        <span class="font-bold cursor-pointer">Б</span>
                        @else
                        <span class="font-bold cursor-pointer">К</span>
                        @endif
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorApplicationsCertificateCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorApplicationsDiplomaBachelorCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorApplicationsDiplomaCollegeCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorContractsCertificateCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorContractsDiplomaBachelorCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorContractsDiplomaCollegeCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="bachelorPaidContractsCount.{{ $value_bachelor->id }}" wire:key="{{ $value_bachelor->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(count($bachelor) == 0 && $levelEducation == 6)
    <x-alert type="info" title="Информация" message="Для выбранной формы обучения приемная кампания не проводится" />
    @endif

    @if(count($mastersDegree) != 0)
    <div class="w-admission-campaign h-admission-campaign overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <table class="w-full text-xs lg:text-sm">
            <thead class="z-10 sticky top-0 bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">Направление подготовки</th>
                    <th scope="col" class="w-16 px-2 py-2 text-center font-semibold">Основа</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Заявления</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Договора</th>
                    <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Оплачено</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mastersDegree as $value_masters_degree)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $value_masters_degree->id }}</td>
                    <td class="z-0 sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">
                        {{ $value_masters_degree->speciality_code }} {{ $value_masters_degree->speciality_name }} @if(!is_null($value_masters_degree->speciality_profile)) ({{ $value_masters_degree->speciality_profile }}) @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value_masters_degree->funding_type == 0)
                        <span class="font-bold cursor-pointer">Б</span>
                        @else
                        <span class="font-bold cursor-pointer">К</span>
                        @endif
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="mastersDegreeApplicationsCount.{{ $value_masters_degree->id }}" wire:key="{{ $value_masters_degree->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="mastersDegreeContractsCount.{{ $value_masters_degree->id }}" wire:key="{{ $value_masters_degree->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="mastersDegreePaidContractsCount.{{ $value_masters_degree->id }}" wire:key="{{ $value_masters_degree->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(count($mastersDegree) == 0 && $levelEducation == 7)
    <x-alert type="info" title="Информация" message="Для выбранной формы обучения приемная кампания не проводится" />
    @endif

    @if(count($phd) != 0)
    <div class="w-admission-campaign h-admission-campaign overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <table class="w-full text-xs lg:text-sm">
            <thead class="z-10 sticky top-0 bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">Направление подготовки</th>
                    <th scope="col" class="w-16 px-2 py-2 text-center font-semibold">Основа</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Заявления</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Договора</th>
                    <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Оплачено</th>
                </tr>
            </thead>
            <tbody>
                @foreach($phd as $value_phd)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="z-0 sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">
                        {{ $value_phd->speciality_code }} {{ $value_phd->speciality_name }} @if(!is_null($value_phd->speciality_profile)) ({{ $value_phd->speciality_profile }}) @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value_phd->funding_type == 0)
                        <span class="font-bold cursor-pointer">Б</span>
                        @else
                        <span class="font-bold cursor-pointer">К</span>
                        @endif
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="phdApplicationsCount.{{ $value_phd->id }}" wire:key="{{ $value_phd->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="phdContractsCount.{{ $value_phd->id }}" wire:key="{{ $value_phd->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-20 px-2 py-2">
                        <input wire:model="phdPaidContractsCount.{{ $value_phd->id }}" wire:key="{{ $value_phd->id }}" type="text" class="w-20 border border-gray-300 rounded px-2 text-sm md:text-base text-center text-gray-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(count($phd) == 0 && $levelEducation == 4)
    <x-alert type="info" title="Информация" message="Для выбранной формы обучения приемная кампания не проводится" />
    @endif
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