<!doctype html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        {!! file_get_contents(resource_path('views/pdf/style.css')) !!}
    </style>
</head>

<body>

    <h2>{{ __('pdf.title') }}</h2>

    <div class="content">
        {{ $text }}
    </div>

    <div class="qr-wrapper">
        <img src="{{ $qr }}" alt="QR Code">
        <div>{{ __('pdf.scan_instruction') }}</div>
    </div>

    <div class="footer">
        {{ __('pdf.generated_at') }} — {{ date('Y-m-d H:i') }}
    </div>

</body>

</html>
