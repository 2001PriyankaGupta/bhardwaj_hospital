<!DOCTYPE html>
<html>

<head>
    <title>Welcome to Medical Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #ff4900;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            padding: 30px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-top: none;
        }

        .credentials {
            background-color: #fff;
            border: 2px dashed #ff4900;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .button {
            display: inline-block;
            background-color: #ff4900;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Welcome to Our Medical Platform</h1>
    </div>

    <div class="content">
        <p>Dear Dr. {{ $doctor->first_name }} {{ $doctor->last_name }},</p>

        <p>Welcome to our medical platform! Your account has been successfully created by the administrator.</p>

        <div class="credentials">
            <h3 style="color: #ff4900; margin-top: 0;">Your Login Credentials:</h3>
            <p><strong>Email:</strong> {{ $doctor->email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
            <p><small>For security reasons, please change your password after first login.</small></p>
        </div>


        <a href="{{ url('/admin/login') }}" class="button">Login to Your Account</a>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
            Medical Platform Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} Medical Platform. All rights reserved.</p>
    </div>
</body>

</html>
