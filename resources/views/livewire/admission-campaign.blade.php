<div x-data="{ hovered: null }" class="flex flex-row flex-wrap">
    @can('access-to-enter-admission-campaign')
    <a x-bind:class="{ 'hovered': hovered === 'admission_campaign_stats' }" @mouseover="hovered = 'admission_campaign_stats'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('admission.campaign-stats') }}">
        <div class="w-full md:w-[23rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Ввод статистики приемной кампании</div>
            <div class="grow px-4 py-2 text-sm text-justify">В данном разделе осуществляется ввод и систематизация статистических данных, связанных с процессом приема абитуриентов в Университет</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'admission_campaign_stats', 'w-6': hovered != 'admission_campaign_stats' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    @endcan
    <a x-bind:class="{ 'hovered': hovered === 'admission_campaign_stats_to_day_report' }" @mouseover="hovered = 'admission_campaign_stats_to_day_report'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('print.admission.campaign.to.day.report') }}">
        <div class="w-full md:w-[23rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Оперативный отчет о ходе приемной кампании за {{ now()->format('d.m.Y') }}</div>
            <div class="grow px-4 py-2 text-sm text-justify">В этом разделе предоставляется оперативный отчет, отражающий динамику и результаты проводимой приемной кампании Университета за текущий день</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'admission_campaign_stats_to_day_report', 'w-6': hovered != 'admission_campaign_stats_to_day_report' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>
    <a x-bind:class="{ 'hovered': hovered === 'admission_campaign_stats_full_report' }" @mouseover="hovered = 'admission_campaign_stats_full_report'" @mouseout="hovered = null" class="mt-2 ml-2" href="{{ route('print.admission.campaign.full.report') }}">
        <div class="w-full md:w-[23rem;] h-56 flex flex-col hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-400/20 rounded cursor-pointer">
            <div class="h-14 px-4 py-2 font-bold">Оперативный отчет о ходе приемной кампании (накопительный)</div>
            <div class="grow px-4 py-2 text-sm text-justify">В этом разделе предоставляется оперативный отчет, отражающий динамику и результаты проводимой приемной кампании Университета</div>
            <div class="h-8 px-4 py-2">
                <div x-bind:class="{ 'w-full': hovered === 'admission_campaign_stats_full_report', 'w-6': hovered != 'admission_campaign_stats_full_report' }" class="bg-gray-600 dark:bg-white border border-slate-400/20 rounded p-0.5 transition-all duration-500"></div>
            </div>
        </div>
    </a>

</div>