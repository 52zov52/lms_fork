<div class="flex flex-col space-y-2">
   <button wire:click="searchGroups">Поиск групп</button>
   <a href="{{ route('e.journal') }}">Журнал</a>

   <table>
      <tr>
         <td>№ пп</td>
         <td>Группа</td>
         <td></td>
      </tr>
      @foreach($groups as $value)
      <tr>
         <td>{{ $loop->iteration }}</td>
         <td>{{ $value->group_name }}</td>
         <td ><a href="{{ route('available.courses', ['groupId' => $value->id]) }}">Клик</a></td>
      </tr>
      @endforeach
   </table>

</div>