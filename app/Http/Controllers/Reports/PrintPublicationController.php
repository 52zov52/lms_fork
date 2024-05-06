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
        $data = MyPublication::where('user_id', '=', Auth::user()->id)->get();

        $filename = time() . '.pdf';
        $pdf = PDF::loadView('reports.pdf.my-publications', ['data' => $data]);

        return $pdf->download($filename);
    }
}
