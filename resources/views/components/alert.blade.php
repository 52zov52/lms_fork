<div @if(!$static) x-data="{show: true}" x-show="show" @endif
    @class([
        'flex flex-col space-y-2 border-t-4 rounded px-4 py-4 text-xs sm:text-sm md:text-sm lg:text-base xl:text-base text-white text-justify font-medium' => true,
        'bg-sky-500 border-t-sky-600' => $type === 'info',
        'bg-orange-500 border-t-orange-600' => $type === 'warning',
        'bg-green-500 border-t-green-600' => $type === 'success',
        'bg-red-500 border-t-red-600' => $type === 'error'
    ]) role="alert">

    <div class="flex {{ (!$static) ? 'justify-between' : '' }}">
        <div class="inline-flex items-baseline space-x-2">
            @if($type == 'info')
                <i class="fa-solid fa-circle-info fa-lg"></i>
            @elseif($type == 'warning')
                <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            @elseif($type == 'success')
                <i class="fa-solid fa-circle-check fa-lg"></i>
            @else
                <i class="fa-solid fa-xmark fa-lg"></i>
            @endif
            <span class="font-semibold">{{ $header }}</span>
        </div>
        @if(!$static)
        <button x-on:click="show=false" class="transform rotate-0 hover:rotate-180 hover:transition hover:duration-700 hover:ease-in-out">
            <i class="fa-solid fa-xmark fa-lg"></i>
        </button>
        @endif
    </div>

    <span>{{ $message }}</span>
</div>