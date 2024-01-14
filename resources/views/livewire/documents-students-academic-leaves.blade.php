<div class="flex flex-col space-y-2">
    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="flex flex-col space-y-2">
            <ul class="list-disc px-4">
                <li><a wire:click="redirectToPersonalRecordsCheckSheet" href="#" class="hover:font-semibold hover:underline">Лист проверки ЛД</a></li>
                <li><a wire:click="redirectToFormZ01" href="#" class="hover:font-semibold hover:underline">Форма "З01"</a></li>
                <li><a wire:click="redirectToFormClaimTemplateDP01" href="#" class="hover:font-semibold hover:underline">Форма "Шаблон претензии ДП01"</a></li>
                <li><a wire:click="printDocumentAcademicLeavesAA01" href="#" class="hover:font-semibold hover:underline">Форма "ДС01"</a></li>
                <li><a href="#" class="hover:font-semibold hover:underline">Форма "П01"</a></li>
            </ul>
        </div>
    </div>
</div>