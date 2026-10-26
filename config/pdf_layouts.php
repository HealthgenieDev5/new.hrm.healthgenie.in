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

];
