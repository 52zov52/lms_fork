<div>
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
         <td>{{ $value }}</td>
         <td ><a wire:click="getDisciplines({{ $value }})" href="#">Клик</a></td>
      </tr>
      @endforeach
   </table>

</div>