<div class="flex flex-col space-y-2 w-full bg-white dark:bg-slate-700 border rounded border-slate-400/20 p-6">
    <p>Документ от {{ \Carbon\Carbon::parse($data->document_date)->format('d.m.Y') }} №{{ $data->document_number }}</p>
    <p>выданный: {{ $data->last_name_dative }} {{ $data->first_name_dative }} {{ $data->middle_name_dative }}</p>
    <div class="flex items-center justify-center">
        <span class="bg-green-500 rounded-md px-4 py-2 text-center text-white font-semibold">Действителен</span>
    </div>
</div>