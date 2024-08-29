<div>
    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    {{ $documentType }}
    <select wire:model="documentType" wire:change="setDocumentType" name="documentType" id="document-type">
        <option value="0">Выберите из списка тип справки</option>
        @foreach($listDocumentTypes as $key => $value)
        <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
    <button type="button" onclick="showToast('sendRequest')">Отправить заявку</button>
</div>

@push('scripts')
<script>
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