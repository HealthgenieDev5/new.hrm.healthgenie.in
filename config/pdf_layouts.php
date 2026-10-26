<?php

/*
|--------------------------------------------------------------------------
| Attendance PDF header layouts per PSP
|--------------------------------------------------------------------------
|
| Each PSP's attendance PDF renders its worker-info / summary header with a
| fixed layout from resources/views/reports/attendance-report/headers.
| PSPs not listed here (including any created later) use the default layout.
| Keep these assignments fixed — changing them changes how that PSP's PDFs look.
|
*/

return [

    'default' => 1,

    'count' => 8,

    // psp_id => layout number
    'psp' => [
        5 => 2, // Shree Balaji (BL)
        6 => 3, // Pari Enterprises (PE)
        7 => 4, // Royal Services (RS)
        11 => 5, // Heuer International (HN)
        12 => 6, // Devrath Security Pvt Ltd (DS)
        13 => 7, // Anjali Facility Management Services (AS)
        14 => 8, // Trinetra Manpower Solution (TM)
    ],

    // layout number => font for that layout's PDFs (files in resources/fonts/pdf).
    // Layouts without an entry use dompdf's default font (serif).
    // line_height keeps rows as tall as with Times so a worker's month stays on one page:
    // dompdf scales line height by each font's own height, so line_height = 1.2 * 10.89 / font height
    // (font height = dompdf FontMetrics::getFontHeight(font, 11); 10.89 is Times).
    'fonts' => [
        2 => ['family' => 'Roboto', 'regular' => 'Roboto-Regular.ttf', 'bold' => 'Roboto-Bold.ttf', 'line_height' => 0.922],
        3 => ['family' => 'Lato', 'regular' => 'Lato-Regular.ttf', 'bold' => 'Lato-Bold.ttf', 'line_height' => 0.900],
        4 => ['family' => 'Open Sans', 'regular' => 'OpenSans-Regular.ttf', 'bold' => 'OpenSans-Bold.ttf', 'line_height' => 0.793],
        5 => ['family' => 'PT Sans', 'regular' => 'PTSans-Regular.ttf', 'bold' => 'PTSans-Bold.ttf', 'line_height' => 0.835],
        6 => ['family' => 'Source Sans 3', 'regular' => 'SourceSans3-Regular.ttf', 'bold' => 'SourceSans3-Bold.ttf', 'line_height' => 0.758],
        7 => ['family' => 'Noto Serif', 'regular' => 'NotoSerif-Regular.ttf', 'bold' => 'NotoSerif-Bold.ttf', 'line_height' => 0.793],
        8 => ['family' => 'Fira Sans', 'regular' => 'FiraSans-Regular.ttf', 'bold' => 'FiraSans-Bold.ttf', 'line_height' => 0.900],
    ],

];
