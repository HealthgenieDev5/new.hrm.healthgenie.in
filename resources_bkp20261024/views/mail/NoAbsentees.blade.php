<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero Absentees – Great News!</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #edf7f0;
            padding: 30px 16px;
            color: #2d4a35;
        }
        .wrapper {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(39, 174, 96, 0.15);
            border-top: 6px solid #27ae60;
        }
        .header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            padding: 36px 32px 28px;
            text-align: center;
            color: #ffffff;
        }
        .header .icon {
            font-size: 52px;
            display: block;
            margin-bottom: 12px;
            line-height: 1;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
        }
        .header p {
            font-size: 13.5px;
            opacity: 0.9;
        }
        .content {
            padding: 32px 32px 24px;
        }
        .message-box {
            background-color: #f0faf4;
            border: 1px solid #a9dfc0;
            border-radius: 8px;
            padding: 20px 22px;
            margin-bottom: 28px;
            font-size: 14.5px;
            line-height: 1.8;
            color: #2d4a35;
        }
        .message-box strong {
            color: #1e8449;
        }
        .stats-row {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            flex: 1;
            background: linear-gradient(135deg, #eafaf1, #d5f5e3);
            border: 1px solid #a9dfc0;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #1e8449;
            line-height: 1;
            margin-bottom: 6px;
        }
        .stat-card .stat-label {
            font-size: 12px;
            color: #5d8a6a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .divider {
            border: none;
            border-top: 1px solid #d5f5e3;
            margin: 0 0 24px;
        }
        .quote {
            text-align: center;
            font-style: italic;
            color: #5d8a6a;
            font-size: 13.5px;
            padding: 0 16px;
            margin-bottom: 4px;
        }
        .footer {
            background-color: #f0faf4;
            text-align: center;
            padding: 16px 32px;
            font-size: 12px;
            color: #7fb890;
            border-top: 1px solid #d5f5e3;
        }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header">
            <span class="icon">&#127881;</span>
            <h1>Excellent Attendance Today!</h1>
            <p>No worker has been absent for more than 2 consecutive days</p>
        </div>

        <div class="content">

            {{-- Message --}}
            <div class="message-box">
                Dear HR Team,<br><br>
                We are pleased to inform you that for the period
                <strong>{{ $from_date }}</strong> to <strong>{{ $to_date }}</strong>,
                <strong>no active worker</strong> has been continuously absent for
                3 or more days.<br><br>
                This reflects the team's strong commitment and dedication.
                Keep up the great work! &#127775;
            </div>

            {{-- Stats --}}
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-value">0</div>
                    <div class="stat-label">Long-term Absentees</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $total_active }}</div>
                    <div class="stat-label">Active Workers Monitored</div>
                </div>
            </div>

            <hr class="divider">

            <p class="quote">
                "A team that shows up every day, builds something great every day."
            </p>

        </div>

        {{-- Footer --}}
        <div class="footer">
            This is a system-generated email. Please do not reply to this message.<br>
            &copy; {{ date('Y') }} Healthgenie HRM &mdash; Absenteeism Monitoring System
        </div>

    </div>
</body>
</html>
