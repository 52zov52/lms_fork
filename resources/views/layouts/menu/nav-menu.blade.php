<ul class="mt-8 flex flex-col space-y-4 px-2 ">
    <li x-data="{ isTooltipVisible: false }" @mouseover.away="isTooltipVisible = false" class="relative bg-blue-100 cursor-pointer">
        <a @mouseover="isTooltipVisible = true" href="#">
            RRRRRRR
        </a>
        <span x-show="isSidebarExpanded || isTooltipVisible || isMobileMenuVisible"
            :class="{ 'tooltip': !isSidebarExpanded && isTooltipVisible && !isMobileMenuVisible, 'p-2': isSidebarExpanded || isMobileMenuVisible }">
            Главная
        </span>
    </li>
    <li x-data="{ isTooltipVisible: false }" @mouseover.away="isTooltipVisible = false" class="relative cursor-pointer">
        <a @mouseover="isTooltipVisible = true" href="#">
            RRRRRR
        </a>
        <span x-show="isSidebarExpanded || isTooltipVisible || isMobileMenuVisible"
            :class="{ 'tooltip': !isSidebarExpanded && isTooltipVisible && !isMobileMenuVisible, 'p-2': isSidebarExpanded || isMobileMenuVisible }">
            ЭБС
        </span>
    </li>
</ul>