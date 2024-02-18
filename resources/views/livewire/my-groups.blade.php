<div class="flex flex-col space-y-2">
   <div class="w-full bg-white rounded shadow-xl p-4">
      <table class="w-full bg-white border shadow-md text-xs">
         <tbody>
            <tr class="bg-gray-100">
               <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
               <td class="w-auto border px-2 py-2 font-semibold">Группа</td>
               <td class="w-32 border px-2 py-2 text-center font-semibold">Действия</td>
            </tr>
            @foreach($groups as $value)
            <tr>
               <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
               <td class="w-auto border px-2 py-2">{{ $value->group_name }}</td>
               <td class="w-32 border px-2 py-2 text-center">
                  <button wire:click="showTeacherCourse({{ $value->id }})" class="bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Журнал группы">
                     <i class="fa-solid fa-address-book"></i>
                  </button>
                  <button wire:click="getAvailableCourses({{ $value->id }})" class="bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Список доступных дисциплин (модулей)">
                     <i class="fa-solid fa-plus"></i>
                  </button>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>