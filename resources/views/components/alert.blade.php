<div @class([ 'flex flex-col space-y-2 border rounded border-slate-200 dark:border-slate-400/20 px-4 py-4 text-xs sm:text-sm md:text-sm lg:text-base xl:text-base text-justify font-medium' => true,
    'bg-sky-400 dark:bg-slate-700 text-white dark:text-sky-400' => $type === 'info',
    'bg-orange-400 dark:bg-slate-700 text-white dark:text-orange-400' => $type === 'warning',
    'bg-green-400 dark:bg-slate-700 text-white dark:text-green-400' => $type === 'success',
    'bg-red-400 dark:bg-slate-700 text-white dark:text-red-400' => $type === 'error'
    ]) role="alert">

    <div class="inline-flex items-baseline space-x-2">
        @if($type == 'info')
        <i class="fa-solid fa-circle-info fa-lg"></i>
        @elseif($type == 'warning')
        <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
        @elseif($type == 'success')
        <i class="fa-solid fa-circle-check fa-lg"></i>
        @else
        <i class="fa-solid fa-xmark fa-lg"></i>
        @endif
        <span class="font-semibold">{{ $title }}</span>
    </div>

    <span>{{ $message }}</span>
</div>