<div id="toast" @class([ 'fixed transition delay-150 duration-300 ease-in-out w-auto top-[4.5rem] right-5 rounded shadow-md p-4 text-xs sm:text-sm md:text-sm lg:text-base xl:text-base text-white font-medium translate-x-full opacity-0' => true,
    'bg-sky-500' => $type === 'info',
    'bg-orange-500 ' => $type === 'warning',
    'bg-green-500' => $type === 'success',
    'bg-red-500' => $type === 'error'
    ])>
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
        <span class="font-semibold">{{ $message }}</span>
    </div>
</div>