<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Validator;

class PdfController extends Controller
{
    /**
     * Generate PDF from posted text.
     *
     * - Validates JWT token using centralized config('technical.jwt_secret')
     * - Validates input text
     * - Generates QR (SVG) using config('technical.qr_target_url')
     * - Renders a Blade view to HTML and converts to PDF
     * - Saves PDF to public disk and returns URL
     */
    public function generate(Request $request)
    {
        // Only POST allowed
        if (!$request->isMethod('post')) {
            return response()->json([
                'success' => false,
                'error' => __('messages.method_not_allowed')
            ], 405);
        }

        // JWT validation
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['success' => false, 'error' => __('messages.unauthorized')], 401);
        }

        try {
            // Use centralized technical config (must match token generator)
            JWT::decode(
                $token,
                new Key(config('technical.jwt_secret'), 'HS256')
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => __('messages.invalid_token')], 401);
        }

        // VALIDATION
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|min:1|max:5000'
        ], [
            'text.required' => __('pdf.validation_required'),
            'text.min'      => __('pdf.validation_required'),
            'text.max'      => __('pdf.validation_max'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->errors()->first('text')
            ], 422);
        }

        $text = $request->input('text');

        // Generate QR (SVG) using centralized config for target url
        $qrTarget = config('technical.qr_target_url', 'https://example.com');

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($qrTarget);

        $qrBase64 = base64_encode($qrSvg);
        $qrDataUri = 'data:image/svg+xml;base64,' . $qrBase64;

        // Generate HTML from Blade
        $html = view('pdf.template', [
            'text' => $text,
            'qr'   => $qrDataUri
        ])->render();

        // Generate PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        // Save PDF
        $filename = 'pdf/' . uniqid('file_', true) . '.pdf';
        Storage::disk('public')->put($filename, $output);

        return response()->json([
            'success' => true,
            'pdf_url' => url('storage/' . $filename)
        ]);
    }
}
