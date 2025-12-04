<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">

    <!-- External CSS -->
    <style>
        {!! file_get_contents(config('technical.pdf.css_path')) !!}
    </style>
</head>
<body>

<h2>{{ __('messages.submitted_content_title') }}</h2>

<div class="content">
    {{ $content }}
</div>

<div class="qr">
    <h3>{{ __('messages.scan_to_visit') }}</h3>
    <img src="{{ $qrDataUri }}" alt="QR">
    <p>
        {{ __('messages.link_text') }}:
        <a href="{{ $qrTarget }}">{{ $qrTarget }}</a>
    </p>
</div>

</body>
</html>
