<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .email-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #f04325 0%, #f46e56 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 30px;
        }

        .email-content {
            background: #f8f9fc;
            border-left: 4px solid #f04325;
            padding: 15px;
            margin: 20px 0;
        }

        .email-footer {
            background: #eaecf4;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #6e707e;
        }

        .button {
            display: inline-block;
            background: #f04325;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }

        .dynamic-content {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .recipient-info {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 4px;
            padding: 10px;
            margin: 15px 0;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ $template->subject ?? 'Notification' }}</h1>
            <p>From: {{ config('app.name') }}</p>
        </div>

        <div class="email-body">
            <!-- Template Information -->
            <div class="recipient-info">
                <strong>Template:</strong> {{ $template->name ?? 'N/A' }}<br>
                <strong>Type:</strong> {{ strtoupper($template->type ?? 'email') }}<br>
                <strong>Scheduled:</strong> {{ $scheduled_at->format('M d, Y H:i') ?? now()->format('M d, Y H:i') }}
            </div>

            <!-- Dynamic Content Section -->
            <div class="email-content">
                <h3>Message Content:</h3>
                <div class="dynamic-content">
                    {!! $content ?? 'No content available' !!}
                </div>
            </div>

            <!-- If there are any action buttons or links -->
            @if (isset($variables['action_url']) && isset($variables['action_text']))
                <div style="text-align: center; margin: 25px 0;">
                    <a href="{{ $variables['action_url'] }}" class="button">{{ $variables['action_text'] }}</a>
                </div>
            @endif

            <!-- Additional dynamic sections -->
            @if (isset($variables['sections']))
                @foreach ($variables['sections'] as $section)
                    <div style="margin: 20px 0; padding: 15px; background: #f8f9fc; border-radius: 4px;">
                        <h4>{{ $section['title'] ?? '' }}</h4>
                        <p>{{ $section['content'] ?? '' }}</p>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
            @if (isset($variables['unsubscribe_url']))
                <p><a href="{{ $variables['unsubscribe_url'] }}" style="color: #6e707e;">Unsubscribe from these
                        notifications</a></p>
            @endif
        </div>
    </div>
</body>

</html>
