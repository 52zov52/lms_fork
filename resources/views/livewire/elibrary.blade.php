<div class="flex flex-col space-y-2">
    @foreach($elibraries as $elibrary)
    <div class="flex flex-col">
        <p><a href="{{ $elibrary->url }}" class="text-blue-500 font-semibold hover:underline">{{ $elibrary->name }}</a></p>
        <p class="text-justify">{!! $elibrary->description !!}</p>
    </div>
    @endforeach
</div>