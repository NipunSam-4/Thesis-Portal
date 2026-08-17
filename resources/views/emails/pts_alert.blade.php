<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'PhD Portal Alert' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e1e8ed;
        }
        .email-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 24px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .email-header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px;
            line-height: 1.6;
        }
        .email-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-top: 0;
            margin-bottom: 16px;
        }
        .details-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .details-box p {
            margin: 6px 0;
            font-size: 14px;
        }
        .btn-action {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }
        .btn-action:hover {
            background-color: #1d4ed8;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>PhD Thesis Portal (PTS)</h1>
            <p>{{ $senderDeskName ?? 'Official Communication System' }}</p>
        </div>
        
        <div class="email-body">
            @if(!empty($title))
                <h2 class="email-title">{{ $title }}</h2>
            @endif

            <p>Dear {{ $recipientName ?? 'User' }},</p>

            <div>
                {!! nl2br(e($messageContent ?? $message ?? '')) !!}
            </div>

            @if(!empty($details) && is_array($details))
                <div class="details-box">
                    @foreach($details as $key => $val)
                        <p><strong>{{ $key }}:</strong> {{ $val }}</p>
                    @endforeach
                </div>
            @endif

            @if(!empty($actionUrl))
                <div style="text-align: center; margin-top: 25px;">
                    <a href="{{ $actionUrl }}" class="btn-action">{{ $actionText ?? 'View Details' }}</a>
                </div>
            @endif

            <p style="margin-top: 30px; font-size: 13px; color: #64748b;">
                Regards,<br>
                <strong>{{ $senderDeskName ?? 'PhD Thesis Portal System' }}</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>This is an automated notification from the PhD Thesis Portal System. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
