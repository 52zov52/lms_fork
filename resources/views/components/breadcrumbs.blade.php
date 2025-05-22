<div>
    @props(['items' => []])

    <nav class="flex">
        <ol class="flex items-center flex-wrap whitespace-wrap text-sm">
            @foreach($items as $index => $item)
                <li class="flex items-center">
                    @if($index > 0)
                        <i class="mx-1 fa-solid fa-chevron-right fa-xs"></i>
                    @endif

                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="text-blue-500 hover:underline">
                            {{ $item['title'] }}
                        </a>
                    @else
                        <span class="truncate">
                            {{ $item['title'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>