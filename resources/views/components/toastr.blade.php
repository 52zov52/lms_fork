<div class="fixed transition delay-150 duration-300 ease-in-out
    @if($show)
        translate-x-0 ease-in opacity-100
    @else
        translate-x-full ease-out opacity-0 
    @endif
    w-72 top-16 right-8 bg-green-500 rounded shadow-md p-4 text-white font-medium">
    {{ $message }}
</div>