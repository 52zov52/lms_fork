<div>
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">

                </tr>
            </thead>
            <tbody>
                @foreach($studentDocumentIssuances as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2 text-left">{{ $value->fio }}</td>
                    <td class="w-auto px-2 py-2 text-left">{{ $value->group_name }}</td>
                    <td class="w-auto px-2 py-2 text-left">
                        @if($value->document_type == 1)
                        {{ __('Справка о статусе студента') }}
                        @elseif($value->document_type == 2)
                        {{ __('Справка о статусе студента (для транспортных карт)') }}
                        @else
                        {{ __('Справка-вызов') }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }}</td>
                    <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                    <td>
                        <button wire:click="edit({{ $value->requester_id }})" class="bg-orange-500 hover:bg-orange-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Подтвердить данные в заявке">
                            <i class="fa-solid fa-file-circle-check"></i>
                        </button>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    const element = document.getElementById('modal-action-btn');
    element.addEventListener('click', function(event) {
        event.preventDefault();
        showToast('update');
    });

    async function showToast(methodName) {
        await @this.callMethod(methodName);;

        const toast = document.getElementById("toast");

        if (toast.dataset.notificationType != '') {
            toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

            setTimeout(function() {
                toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
            }, 3500);
        }
    }
</script>
@endpush