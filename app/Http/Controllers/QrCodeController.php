<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generateQrCode()
    {
        return response()->streamDownload(
            function () {
                echo QrCode::size(200)
                    ->format('png')
                    ->generate('https://lms.iubip.ru/qr-code-document-verifications/E9BjX53W');
            },
            'qr-code.png',
            [
                'Content-Type' => 'image/png',
            ]
        );
    }
}
