<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ $title }} - {{ config('app.name') }}</title>

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                height: 100%;
                width: 100%;
                overflow: hidden;
            }

            .pdf-container {
                width: 100%;
                height: 100%;
            }

            .pdf-container iframe,
            .pdf-container embed,
            .pdf-container object {
                width: 100%;
                height: 100%;
                border: none;
            }
        </style>
    </head>

    <body>
        <div class="pdf-container">
            <iframe
                src="{{ route('pdf.stream', $filename) }}"
                type="application/pdf"
                title="{{ $title }}"
            >
                <p>
                    {{ __('Your browser does not support PDF viewing.') }}
                    <a href="{{ route('pdf.stream', $filename) }}">{{ __('Download the PDF') }}</a>
                </p>
            </iframe>
        </div>
    </body>

</html>
