<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @if(count($materials) != 0)
            @foreach($materials as $material)
            <div class="bg-gray-100 dark:bg-slate-700 flex flex-col space-y-2 border rounded border-slate-400/20 px-4 py-2">
                <p class="text-sm font-semibold">{{ $material['fio'] }}</p>
                <p class="text-sm">{{ $material['created_at'] }}</p>
                <div class="text-sm">
                    {!! nl2br(e($material['comment'])) !!}
                </div>
                @if(!is_null($material['files']))
                <div class="flex flex-col space-y-2 border rounded border-slate-400/20 px-4 py-2">
                    <p class="text-sm font-semibold">Учебный материал:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($material['files'] as $value)
                        <li>
                            <a class="text-blue-500 hover:underline" href="{{ asset('storage/' . $value['path_to_file']) }}" target="_blank" rel="noopener noreferrer">{{ $value['original_filename'] }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="flex justify-end">
                    <button onclick="confirmDestroy('{{ $material['id'] }}')" class="text-red-500 underline">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
            @endforeach
        @else
        <x-alert type="info" title="Информация" message="Учебный материал по дисциплине не загружен" />
        @endif
        <form enctype="multipart/form-data">
            @csrf
            <div class="flex flex-col space-y-4">
                <div class="flex flex-col space-y-2">
                    <label for="comment" class="text-sm font-semibold">Комментарий к учебному материалу:</label>
                    <textarea class="w-full
                        dark:bg-slate-700
                        border 
                        border-slate-400/20 
                        rounded 
                        px-2 
                        focus:outline-none 
                        focus:border-blue-500 
                        focus:ring-1 
                        focus:ring-blue-500"
                        wire:model="comment" name="comment" id="comment" cols="5" rows="5">
                    </textarea>
                </div>

                <div class="flex flex-col">
                    <div class="flex text-sm">
                        <label for="file" class="font-semibold">Файл(ы)</label>
                        <input wire:model="files" type="file" name="file" id="file-{{ $rand }}" multiple class="ml-1 w-full">
                    </div>
                    <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.doc, *.docx, *.xls, *.xlsx, *.jpg, *.bmp, *.png, *.zip</span>
                    <span class="text-xs font-semibold">максимальный объем файла: 9 МБ</span>
                    <div wire:loading wire:target="files" class="text-green-500 text-sm font-semibold">Идет загрузка файла ...</div>
                    @error('files') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    @if(count($files) != 0)
                    <div class="mt-2 flex flex-col bg-gray-100 dark:bg-slate-700 border border-slate-400/20 rounded p-2 text-sm">
                        <p class="text-sm font-semibold">Прикреплённые файлы:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($files as $key => $value)
                            <li>
                                {{ $value->getClientOriginalName() }}
                                <button type="button" wire:click="removeFileFromArray({{ $key }})" class="ml-2 text-red-500 underline">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="flex md:justify-end">
                    <button wire:click="store" type="button" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                        <span wire:loading.remove wire:target="store">
                            {{ 'Добавить' }}
                        </span>
                        <span wire:loading wire:target="store">
                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function confirmDestroy(id) {
        if (confirm('Вы уверены, что хотите удалить данную запись?')) {
            Livewire.dispatch('destroy', {
                assignmentCommentId: id
            });
        }
    }
</script>
@endpush