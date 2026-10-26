{{-- Per-PSP PDF fonts (config/pdf_layouts.php 'fonts'); include inside the template's <style> --}}
@foreach (\App\Models\Psp::pdfFontFaces() as $font)
        @font-face {
            font-family: '{{ $font['family'] }}';
            font-style: normal;
            font-weight: normal;
            src: url("{{ $font['regular'] }}") format("truetype");
        }

        @font-face {
            font-family: '{{ $font['family'] }}';
            font-style: normal;
            font-weight: bold;
            src: url("{{ $font['bold'] }}") format("truetype");
        }
@endforeach
