<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Thesis Management System Alert' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 30px 15px;
            box-sizing: border-box;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .email-body {
            padding: 32px 30px;
            line-height: 1.65;
            color: #334155;
            font-size: 15px;
        }
        .email-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 16px;
            line-height: 1.3;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 9999px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-green { background-color: #dcfce7; color: #166534; }
        .badge-amber { background-color: #fef3c7; color: #92400e; }
        .badge-rose { background-color: #ffe4e6; color: #9f1239; }
        .badge-purple { background-color: #f3e8ff; color: #6b21a8; }
        
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 25px 0;
        }
        p {
            margin: 0 0 16px 0;
        }
        p:last-child {
            margin-bottom: 0;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            @include('emails.components.header', ['senderDeskName' => $senderDeskName ?? 'Thesis Management System'])
            
            <div class="email-body">
                @yield('content')
            </div>

            @include('emails.components.footer')
        </div>
    </div>
</body>
</html>
