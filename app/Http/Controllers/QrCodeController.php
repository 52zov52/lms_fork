<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function saveQrCode()
    {
        $alphabet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = substr(str_shuffle($alphabet), 0, 8);

        $qrCode = QrCode::size(200)
            ->format('png')
            ->generate("https://lms.iubip.ru/qr-code-document-verifications/$code");
        
        $filename = time() . '.png';
        $imgPath = 'temp/' . $filename;

        Storage::disk('public')->put($imgPath, $qrCode);

        return ['imgPath' => $imgPath, 'filename' => $filename, 'code' => $code];
    }
}
