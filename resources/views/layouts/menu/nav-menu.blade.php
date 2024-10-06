<ul class="flex flex-col items-center space-y-2 px-2 py-2 text-sm">
    <li @mouseover="isTooltipVisible = true" @mouseover.away="isTooltipVisible = false" class="{{ (request()->routeIs('dashboard')) ? 'bg-blue-500 text-white' : 'hover:bg-blue-500 hover:text-white' }} relative cursor-pointer rounded">
        <a href="{{ route('dashboard') }}" class="w-10 h-8 flex items-center justify-center space-x-2 px-2 py-3">
            <i class="fa-solid fa-house fa-lg"></i>
        </a>
        <span x-show="isTooltipVisible" 
        class="absolute top-1/2 -right-16">
            Главная
        </span>
    </li>
</ul>