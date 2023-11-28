<div class="flex flex-col space-y-2">
    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="flex flex-col space-y-2">
            @foreach($elibraries as $elibrary)
            <div class="flex flex-col">
                <p><a href="{{ $elibrary->url }}" class="text-blue-500 hover:text-blue-600 hover:underline font-semibold">{{ $elibrary->name }}</a></p>
                <p>{!! $elibrary->description !!}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>