<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absenteeism Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .wrapper {
            max-width: 700px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #c0392b;
            color: #ffffff;
            padding: 24px 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
        .content {
            padding: 24px 30px;
        }
        .summary-box {
            background-color: #fdf3f2;
            border-left: 4px solid #c0392b;
            border-radius: 4px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .summary-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
        }
        .summary-box strong {
            color: #c0392b;
        }
        h2 {
            font-size: 16px;
            color: #c0392b;
            margin: 0 0 12px;
            border-bottom: 1px solid #f0d0ce;
            padding-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        thead tr {
            background-color: #c0392b;
            color: #ffffff;
        }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #fdf3f2;
        }
        tbody tr:hover {
            background-color: #fce4e1;
        }
        tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #f0e0de;
            vertical-align: middle;
        }
        .badge {
            display: inline-block;
            background-color: #e74c3c;
            color: #fff;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            text-align: center;
            line-height: 22px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 4px;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #888;
            font-size: 14px;
        }
        .footer {
            background-color: #f4f6f9;
            text-align: center;
            padding: 16px 30px;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <h1>&#9888; Absenteeism Alert</h1>
            <p>Automated report for workers absent for 3 or more consecutive days</p>
        </div>

        <div class="content">

            {{-- Summary --}}
            <div class="summary-box">
                <p>
                    Dear HR Team,<br><br>
                    This is to inform you that the following <strong>{{ $total }} worker(s)</strong>
                    have been continuously absent from
                    <strong>{{ $from_date }}</strong> to <strong>{{ $to_date }}</strong>
                    (3 days or more). Please take necessary action at the earliest.
                </p>
            </div>

            {{-- Table --}}
            <h2>Absentee Workers List</h2>

            @if($absentees->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Employee Code</th>
                        <th>Mobile Number</th>
                        <th>Last Present Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absentees as $index => $worker)
                    <tr>
                        <td><span class="badge">{{ $index + 1 }}</span></td>
                        <td>{{ $worker['name'] }}</td>
                        <td>{{ $worker['employee_code'] }}</td>
                        <td>{{ $worker['mobile_number'] ?? '—' }}</td>
                        <td>{{ $worker['last_present_date'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">
                &#10003; No absentees found for the selected period. All workers are present.
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="footer">
            This is a system-generated email. Please do not reply to this message.<br>
            &copy; {{ date('Y') }} Healthgenie HRM &mdash; Absenteeism Monitoring System
        </div>

    </div>
</body>
</html>
