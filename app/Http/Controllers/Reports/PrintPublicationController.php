<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MyPublication;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;

class PrintPublicationController extends Controller
{
    public function printMyPublications()
    {
        $data = $this->dataTransform(MyPublication::where('user_id', '=', Auth::user()->id)->orderBy('category')->get());

        $filename = time() . '.pdf';
        $pdf = PDF::loadView('reports.pdf.my-publications', ['data' => $data]);

        return $pdf->download($filename);
    }

    public function dataTransform($data)
    {
        $transformedData = collect($data)->groupBy('category')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    $item->title,
                    $item->output_data,
                    $item->volume_full_in_printed_sheets,
                    $item->volume_full_on_pages,
                    $item->name_co_authors
                ];
            });
        });

        return $transformedData;
    }
}
