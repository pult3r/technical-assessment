<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Validator;

class PdfController extends Controller
{
    public function generate(Request $request)
    {
        if (!$request->isMethod('post')) {
            return response()->json([
                'success' => false,
                'error' => __('messages.method_not_allowed')
            ], 405);
        }

        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['success' => false, 'error' => __('messages.unauthorized')], 401);
        }

        try {
            JWT::decode(
                $token,
                new Key(config('technical.jwt_secret'), 'HS256')
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => __('messages.invalid_token')], 401);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'required|string|min:1|max:5000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors()->first('text')
            ], 422);
        }

        $text = $request->input('text');

        /*
        |--------------------------------------------------------------------------
        | Load QR settings from config
        |--------------------------------------------------------------------------
        */
        $qrTarget = config('technical.qr.target_url');
        $qrSize   = config('technical.qr.size');
        $qrMargin = config('technical.qr.margin');
        $qrECC    = config('technical.qr.error_correction');

        $qrSvg = QrCode::format('svg')
            ->size($qrSize)
            ->margin($qrMargin)
            ->errorCorrection($qrECC)
            ->generate($qrTarget);

        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        /*
        |--------------------------------------------------------------------------
        | Render PDF
        |--------------------------------------------------------------------------
        */
        $html = view('pdf.template', [
            'text' => $text,
            'qr'   => $qrDataUri
        ])->render();

        $paper = config('technical.pdf.paper_format');
        $orientation = config('technical.pdf.paper_orientation');

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $output = $dompdf->output();

        $dir = config('technical.pdf.storage_dir');
        $filename = $dir . '/' . uniqid('file_', true) . '.pdf';

        Storage::disk('public')->put($filename, $output);

        return response()->json([
            'success' => true,
            'pdf_url' => url('storage/' . $filename)
        ]);
    }
}
