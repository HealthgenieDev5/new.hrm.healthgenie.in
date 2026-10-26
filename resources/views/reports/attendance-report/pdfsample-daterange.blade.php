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
            color: #6aa5ff
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
                    <th colspan="3">Confirmation / Verification</th>
                </tr>
                <tr>
                    <th colspan="3">
                        <img src="{{ public_path('media/disclaimer/footer.png') }}"
                            style="max-width:100%; max-height: 100%; height:98px; width:100%;" />
                    </th>
                </tr>
                <tr>
                    <th colspan="3">Worker Signature / Thumb Impression</th>
                </tr>
            </thead>
        </table>
    </footer>
    <main>
        @php
            $rangeLabel =
                \Carbon\Carbon::parse($rangeStart)->format('d M Y') .
                ' – ' .
                \Carbon\Carbon::parse($rangeEnd)->format('d M Y');
        @endphp

        @foreach ($pdfData as $data)
            @php
                $worker = $data['worker'];
                $attendance = $data['attendance'];
                $summary = $data['summary'];
                $date_of_joining = \Carbon\Carbon::parse($worker->date_of_joining)->format('d M Y');
            @endphp
            @if ($attendance)
                @php
                    $info = [
                        'PSP Name' => $worker->psp?->name,
                        'Worker name' => $worker->name,
                        'Worker code' => $worker->code,
                        'Joining Date' => $date_of_joining,
                        'Date Range' => $rangeLabel,
                    ];
                    $stats = [
                        'Days in range' => $summary['days_in_month'],
                        'Working Days' => $summary['newTotalWorkingDays'],
                        'Week Offs' => $summary['newWeekOffCount'],
                        'Holiday and RH' => $summary['total_holiday_and_rh'],
                        'Total Paid' => $summary['newTotalPaidDays'],
                        'Deduction days' => $summary['newDeductionDays'],
                    ];
                @endphp
                @include(\App\Models\Psp::pdfHeaderViewFor($worker->psp), compact('info', 'stats'))
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
                        @foreach ($attendance as $record)
                            @php
                                $in_time_processed =
                                    $record?->in_time_processed && !$record?->is_weekoff
                                        ? \Carbon\Carbon::parse($record?->in_time_processed)->format('h:i A')
                                        : '--:--';

                                $out_time_processed =
                                    $record?->out_time_processed && !$record?->is_weekoff
                                        ? \Carbon\Carbon::parse($record?->out_time_processed)->format('h:i A')
                                        : '--:--';

                                $status =
                                    $record?->is_weekoff && in_array($record?->status, ['P', 'H/D', 'A'])
                                        ? 'W/O'
                                        : $record?->status;
                            @endphp
                            <tr>
                                <td>{{ $record->date->format('d M Y') }}</td>
                                <td>{{ $record->date->format('l') }}</td>
                                <td>{{ $in_time_processed }}</td>
                                <td>{{ $out_time_processed }}</td>
                                <td>{{ $status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if (!$loop->last)
                    <div style="page-break-before:always;"> </div>
                @endif
            @endif
        @endforeach
    </main>
</body>

</html>
