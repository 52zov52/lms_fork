<div class="flex flex-col space-y-2">

    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-between">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <select wire:model="level_education_id" wire:change="setLevelEducationId" name="level_education_id" id="level-education-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите уровень образования</option>
                @foreach($listLevelEducations as $levelEducation)
                <option value="{{ $levelEducation->id }}">{{ $levelEducation->level_name }}</option>
                @endforeach
            </select>
            <select wire:model="speciality_id" wire:change="setSpecialityId" name="speciality_id" id="speciality-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите направление подготовки</option>
                @foreach($listSpecialities as $speciality)
                <option value="{{ $speciality->id }}">{{ $speciality->specialty_code }} {{ $speciality->speciality_name }}</option>
                @endforeach
            </select>
            <select wire:model="group_id" wire:change="setGroupId" name="group_id" id="group-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите группу</option>
                @foreach($listGroups as $group)
                <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Дисциплина</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Семестр</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Форма контроля</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">№ ведомости</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Дата окртытия</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Дата закрытия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bulletins as $key => $bulletin)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $bulletin->discipline_name }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $bulletin->semester_number }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $bulletin->control_name }}</td>
                    <td class="w-32 px-2 py-2 text-center"> <a href="{{ route('bulletin', $bulletin->id) }}">{{ $bulletin->bulletin_number }}</a></td>
                    <td class="w-32 px-2 py-2 text-center">{{ $bulletin->opening_date }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $bulletin->closing_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>