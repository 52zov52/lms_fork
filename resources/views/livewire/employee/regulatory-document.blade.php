<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div wire:loading.remove wire:target="fecthData, search">
            @if(!is_null($regulatoryDocuments) && count($regulatoryDocuments) != 0)

            @else
            <x-alert type="info" title="Информация" message="Список документов пуст" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="fecthData, search">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
            </div>
        </div>
    </div>
</div>