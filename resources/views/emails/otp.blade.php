<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 500px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #ff7b00;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px;
            text-align: center;
            background-color: #fff4ed !important;
        }

        .otp-code {
            background: #fff5e6;
            color: #ff7b00;
            font-size: 28px;
            font-weight: bold;
            padding: 12px 25px;
            border-radius: 6px;
            letter-spacing: 3px;
            margin: 20px 0;
            display: inline-block;
            border: 2px dashed #ff7b00;
        }

        .message {
            color: #333;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .footer {
            background: #f9f9f9;
            padding: 15px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #eee;
        }

        .note {
            background: #fff5e6;
            border-radius: 5px;
            padding: 10px;
            margin: 20px 0;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>OTP Verification</h1>
        </div>

        <div class="content">
            <h2 style="color: #ff7b00; margin-bottom: 15px;">Hello, {{ $name }}!</h2>

            <p class="message">
                Your One-Time Password (OTP) for login is:
            </p>

            <div class="otp-code">
                {{ $otp }}
            </div>

            <p class="message">
                This OTP is valid for <strong>10 minutes</strong>.
            </p>

            <div class="note">
                ⚠️ Please do not share this OTP with anyone for security reasons.
            </div>

            <p style="color: #666; font-size: 14px; margin-top: 20px;">
                Thank you for choosing our service.<br>
                Bhardwaj Hospital Team
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Bhardwaj Hospital. All rights reserved.
        </div>
    </div>
</body>

</html>
