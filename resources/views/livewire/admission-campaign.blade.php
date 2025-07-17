<div x-data="{ hovered: null }" class="flex flex-row flex-wrap">
    @can('access-to-enter-admission-campaign')
    <a x-bind:class="{ 'hovered': hovered === 'admission_campaign_stats' }" @mouseover="hovered = 'admission_campaign_stats'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('admission.campaign.stats') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Ввод статистики приемной кампании</div>
            <div class="grow px-4 py-2 text-sm text-justify">В данном разделе осуществляется ввод и систематизация статистических данных, связанных с процессом приема абитуриентов в Университет</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'admission_campaign_stats', 'w-6': hovered != 'admission_campaign_stats' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    @endcan
    <a x-bind:class="{ 'hovered': hovered === 'applicant_average_score' }" @mouseover="hovered = 'applicant_average_score'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('applicant.average.score') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Средний балл аттестата абитуриентов</div>
            <div class="grow px-4 py-2 text-sm text-justify">В данном разделе осуществляется сбор и анализа статистических данных о средних баллах аттестатов абитуриентов, претендующих на поступление в Университет</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'applicant_average_score', 'w-6': hovered != 'applicant_average_score' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'admission_campaign_stats_to_day_report' }" @mouseover="hovered = 'admission_campaign_stats_to_day_report'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('print.admission.campaign.to.day.report') }}">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Оперативный отчет о ходе приемной кампании за {{ now()->format('d.m.Y') }}</div>
            <div class="grow px-4 py-2 text-sm text-justify">В этом разделе предоставляется оперативный отчет, отражающий динамику и результаты проводимой приемной кампании Университета за текущий день</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'admission_campaign_stats_to_day_report', 'w-6': hovered != 'admission_campaign_stats_to_day_report' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a wire:click="generateFullReport" x-bind:class="{ 'hovered': hovered === 'admission_campaign_stats_full_report' }" @mouseover="hovered = 'admission_campaign_stats_full_report'" @mouseout="hovered = null" class="mt-2 ml-2" href="#">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Оперативный отчет о ходе приемной кампании (накопительный)</div>
            <div class="grow px-4 py-2 text-sm text-justify">В этом разделе предоставляется оперативный отчет, отражающий динамику и результаты проводимой приемной кампании Университета</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'admission_campaign_stats_full_report', 'w-6': hovered != 'admission_campaign_stats_full_report' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a wire:click="fullReportParamModalVisible" x-bind:class="{ 'hovered': hovered === 'archive_admission_campaign_stats_full_report' }" @mouseover="hovered = 'archive_admission_campaign_stats_full_report'" @mouseout="hovered = null" class="mt-2 ml-2" href="#">
        <div class="w-full md:w-[22rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Архив оперативных отчетов о ходе приемной кампании (по уч. годам)</div>
            <div class="grow px-4 py-2 text-sm text-justify">В этом разделе представлен архив оперативных отчетов, отражающий динамику и результаты проводимых приемных кампаний Университета по каждому уч. году</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'archive_admission_campaign_stats_full_report', 'w-6': hovered != 'archive_admission_campaign_stats_full_report' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>

    <!-- Modal dialog -->
    <x-dialog-modal wire:model="isFullReportParamModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма выбора критериев отчета
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <select wire:model="selectedAdmissionCampaignId" name="selected_admission_campaign_id" id="selected-admission-campaign-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="0">Выберите приемную кампанию</option>
                        @foreach($admissionCampaign as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isFullReportParamModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="generateArchiveFullReport" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Сформировать отчет') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>