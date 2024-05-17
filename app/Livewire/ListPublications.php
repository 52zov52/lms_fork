<?php

namespace App\Livewire;

use App\Models\MyPublication;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ListPublications extends Component
{
    public function render()
    {
        $publications = MyPublication::join('users_data', 'my_publications.user_id', '=', 'users_data.user_id')
            ->select(
                'my_publications.id',
                DB::raw("CONCAT(users_data.last_name, ' ', SUBSTR(users_data.first_name, 1, 1), '. ',SUBSTR(users_data.middle_name, 1, 1),'.') as author"),
                'my_publications.status',
                'my_publications.title',
                'my_publications.output_data',
                'my_publications.year_publishing',
                'my_publications.volume_full_on_pages',
                'file_path',
                'link_elibrary'
            )
            ->paginate(50);

        return view('livewire.list-publications', ['publications' => $publications]);
    }

    public function downloadBibliographicDescription($id)
    {
        $txtFileName = time() . '.txt';
        $myPublication = MyPublication::select('bibliographic_description')->where('id', '=', $id)->first();

        return response()->streamDownload(function () use ($myPublication) {
            echo $myPublication->bibliographic_description;
        }, $txtFileName);
    }
}
