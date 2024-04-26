<div class="flex flex-col space-y-2">

   <div class="border rounded border-slate-400/20">
      <table class="w-full text-xs lg:text-sm">
         <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
            <tr class="border-b border-slate-400/20">
               <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
               <th scope="col" class="w-auto px-2 py-2 font-semibold">Группа</th>
               <th scope="col" class="w-20 px-2 py-2 font-semibold">Действия</th>
            </tr>
         </thead>
         <tbody>
            @foreach($groups as $value)
            <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
               <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
               <td class="w-auto px-2 py-2 ">{{ $value->group_name }}</td>
               <td class="w-20 px-2 py-2 text-center">
                  <button wire:click="showTeacherCourse({{ $value->id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-indigo-400 font-bold" title="" data-tippy-content="Журналы группы">
                     <i class="fa-solid fa-address-book"></i>
                  </button>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
   
</div>