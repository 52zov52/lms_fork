<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    @if(!is_null($studentSptConsentStatus) && count($studentSptConsentStatus) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Учебный год</th>
                    <th scope="col" class="w-auto px-2 py-2 text-center font-semibold">Полугодие</th>
                    <th scope="col" class="w-auto px-2 py-2 text-center font-semibold">Статус согласия</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentSptConsentStatus as $key => $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-auto px-2 py-2 text-left">{{ $value->academic_year }}</td>
                    <td class="w-auto px-2 py-2 text-center ">{{ $value->half_year }}</td>
                    <td class="w-auto px-2 py-2 text-center ">
                        @if($value->consent_status)
                        <span class="text-green-400 font-bold cursor-pointer">
                            {{ __('Подтверждена') }}
                        </span>
                        @else
                        <span class="text-red-400 font-bold cursor-pointer">
                            {{ __('Не подтверждена') }}
                        </span>
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        <button onclick="confirmConsentStatus('{{ $value->id }}')" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-1 text-xs text-center text-green-400 hover:text-white" @if($value->consent_status) disabled @endif title="" data-tippy-content="Подтверждение">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        @if($value->consent_status == 3)
                        <button wire:click="downloadInformedConsent({{ $value->id }})" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Скачать согласие на СПТ">
                            <i class="fa-solid fa-download"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <x-alert type="info" title="Информация" message="Согласия на социально-психологическое тестирование отсутствует" />
    @endif
</div>

@push('scripts')
<script>
    async function showToast(methodName, param = null) {
        await @this.callMethod(methodName, param);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }

    function confirmConsentStatus(id) {
        if (confirm('Вы уверены, что хотите подтвердить согласие на СПТ?')) {
            showToast('update', id);
        }
    }
</script>
@endpush