<html>

<head>
    <style>
        body,
        div,
        p {
            margin: 0;
            padding: 0;
        }

        @page {
            margin-top: 215px;
            margin-bottom: 215px;
            font-size: 12px;
        }

        header {
            position: fixed;
            left: 0px;
            right: 0px;
            height: 150px;
            line-height: 150px;
            text-align: center;
            top: -165px;
            margin: 0px;
            padding: 0px;
            color: #6aa5ff;
            /* background-color: #6aa5ff; */
        }

        footer {
            position: fixed;
            left: 0px;
            right: 0px;
            height: 150px;
            bottom: -165px;
            margin: 0px;
            padding: 0px;
            font-size: 10px;
        }

        main {
            margin: 0px;
            padding: 0px;
            height: 100%;
        }

        table {
            border-collapse: collapse;
            color: #181c32;
            vertical-align: top;
            table-layout: fixed;
            width: 100%;
            max-width: max-content;
        }

        th,
        td {
            padding: .25rem .5rem;
        }

        .attendance-table td {
            padding: 0.05rem 0.15rem;
            font-size: 11px;
        }

        .footer-table td {
            padding: 0.05rem 0.15rem;
        }
    </style>
</head>

<body>
    <header></header>
    <footer>
        <table class="footer-table" style="text-align: left">
            <thead>
                <tr>
                    <th colspan="3 ">Disclaimer</th>
                </tr>
                <!-- <tr>
                    <td class="NotoSansDevanagari" colspan="3">1. उपरोक्त नामित व्यक्ति उपरोक्त उल्लिखित PSP ( उत्पादन सेवा प्रदाता ) के कर्मचारी हैं।</td>
                </tr>
                <tr>
                    <td class="NotoSansDevanagari" colspan="3">2. हमारे पास व्यवसाय संचालन के लिए सभी आवश्यक प्रमाणपत्र और लाइसेंस जैसे कि PF पंजीकरण और ESI पंजीकरण हैं।</td>
                </tr>
                <tr>
                    <td class="NotoSansDevanagari" colspan="3">3. हम पुष्टि करते हैं कि हमने कानून के तहत अपनी सभी जिम्मेदारियाँ पूरी रूप से निभाई हैं, जिनमें वेतन, PF और ESI का भुगतान कर्मचारी को उपरोक्त महीने के लिए शामिल है।</td>
                </tr>
                <tr>
                    <td class="NotoSansDevanagari" colspan="3">4. मुझे पिछले महीने के लिए कुल राशि रुपये ...................... प्राप्त हो चुकी है। इस महीने तक कोई राशि बकाया नहीं है।</td>
                </tr>
                <tr>
                    <td class="NotoSansDevanagari" colspan="3">5. उपरोक्त उल्लिखित जानकारी सत्य एवं सही है और इसे मेरे द्वारा पूरी तरह से सत्यापित एवं अनुमोदित किया गया है।</td>
                </tr> -->
                <tr>
                    <th colspan="3">
                        <img src="<?= asset('media/disclaimer/footer.png') ?>" style="max-width:100%; max-height: 100%; height:70px; width:100%;" />
                    </th>
                </tr>
                <tr>
                    <th>Worker Name</th>
                    <th>Worker Signature</th>
                    <th>Worker Thumb Impression</th>
                </tr>
            </thead>
        </table>
    </footer>
    <main>
        @foreach ($pdfData as $data)

        @php
        $worker = $data['worker'];
        $attendance = $data['attendance'];
        $summary = $data['summary'];
        $date_of_joining = \Carbon\Carbon::parse($worker->date_of_joining)->format('d M Y');
        @endphp

        @if ($attendance && $summary['presentOnWeekOff'] > 0)

        <table style="width:100%;">
            <tbody>
                <tr>
                    <td style="width: 40%; vertical-align: top; padding:0px;">
                        <table style="width: max-content; margin-right: auto;" border="1">
                            <tbody>
                                <tr>
                                    <td style="text-align: left">PSP Name</td>
                                    <th style="text-align: left">{{$worker->psp->name}}</th>
                                </tr>
                                <tr>
                                    <td style="text-align: left">Worker name</td>
                                    <th style="text-align: left">{{$worker->name}}</th>
                                </tr>
                                <tr>
                                    <td style="text-align: left">Worker code</td>
                                    <th style="text-align: left">{{$worker->code}}</th>
                                </tr>
                                <tr>
                                    <td style="text-align: left">Joining Date</td>
                                    <th style="text-align: left">{{$date_of_joining}}</th>
                                </tr>
                                <tr>
                                    <td style="text-align: left">Year Month</td>
                                    <th style="text-align: left">{{$attendance?->first()->date->format('M Y')}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 20%; padding:0px;"></td>
                    <td style="width: 40%; vertical-align: top; padding:0px;">
                        <table style="width:100%; text-align: right; " border="1">
                            <tbody>
                                <tr>
                                    <td style="width:75%">Week Off in Month</td>
                                    <th style="width:75%">{{$summary['totalIsWeekOff']}}</th>
                                </tr>
                                <tr>
                                    <td style="width:75%">Working Week Off Days</td>
                                    <th style="width:75%">{{$summary['presentOnWeekOff']}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <table border="1" style="text-align: center" class="attendance-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Punch In</th>
                    <th>Punch Out</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach ( $attendance as $record)

                @if ( $record?->is_weekoff && in_array($record?->status, ['P', 'H/D']) )
                <tr>
                    <td>{{ $record->date->format('d M Y') }}</td>
                    <td>{{ $record->date->format('l') }}</td>
                    <td>{{ $record?->in_time_processed ? \Carbon\Carbon::parse($record?->in_time_processed)->format('h:i A') : "--:--" }}</td>
                    <td>{{ $record?->out_time_processed ? \Carbon\Carbon::parse($record?->out_time_processed)->format('h:i A') : "--:--" }}</td>
                    <td>{{ $record?->status }}</td>
                </tr>
                @endif

                @endforeach
            </tbody>
        </table>

        @if(!$loop->last)
        <div style="page-break-before:always;"> </div>
        @endif

        @endif


        @endforeach
    </main>
</body>

</html>
