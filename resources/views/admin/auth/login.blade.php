<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: flex;
            width: 1000px;
            min-height: 600px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .left-section {
            background: linear-gradient(135deg, #ff4900 0%, #ff6a00 100%);
            width: 45%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }

        .left-section::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
        }

        .left-content {
            color: #fff;
            text-align: center;
            z-index: 1;
            max-width: 380px;
        }

        .hospital-icon {
            font-size: 4.5rem;
            margin-bottom: 25px;
            color: white;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .left-content h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .left-content p {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .features {
            text-align: left;
            margin-top: 30px;
        }

        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .feature i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        .right-section {
            width: 55%;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .logo-icon {
            font-size: 2.2rem;
            color: #ff4900;
            margin-right: 12px;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }

        .login-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #222;
        }

        .login-subtitle {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 40px;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 1rem;
        }

        .input-group input {
            width: 100%;
            padding: 9px 16px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            font-size: 1rem;
            transition: all 0.3s;
            box-sizing: border-box;
        }

        .input-group input:focus {
            border-color: #ff4900;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 73, 0, 0.2);
        }

        .input-icon {
            position: absolute;
            right: 18px;
            top: 38px;
            color: #777;
        }

        .role-selector {
            margin-bottom: 25px;
        }

        .role-selector label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 1rem;
        }

        .role-selector select {
            width: 100%;
            padding: 9px 18px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            font-size: 1rem;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23777' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 16px;
        }

        .role-selector select:focus {
            border-color: #ff4900;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 73, 0, 0.2);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 26px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .forgot-password a {
            color: #ff4900;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            text-decoration: underline;
            color: #e04100;
        }

        .login-btn {
            width: 100%;
            height: 44px;
            background: #ff4900;
            color: white;
            font-size: 1.1rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .login-btn:hover {
            background: #ff4900;
        }

        .login-btn i {
            margin-right: 10px;
        }

        .session-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            font-size: 0.9rem;
            color: #555;
            margin-top: 20px;
        }

        .session-info i {
            color: #ff4900;
            margin-right: 8px;
        }

        /* Alert styling */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            display: none;
        }

        .alert-error {
            background-color: #fee;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        @media (max-width: 900px) {
            .login-container {
                flex-direction: column;
                width: 95%;
                max-width: 500px;
            }

            .left-section,
            .right-section {
                width: 100%;
            }

            .left-section {
                min-height: 300px;
                padding: 30px;
            }

            .right-section {
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="left-section">
            <div class="left-content">
                <div class="hospital-icon">
                    <i class="fas fa-hospital-alt"></i>
                </div>
                <h1>Bhardwaj Hospital Management System</h1>
                <p>Secure access to the complete hospital management dashboard with role-based permissions and advanced
                    session management.</p>

            </div>
        </div>

        <div class="right-section">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-stethoscope"></i>
                </div>
                <div class="logo-text">MediCare Login</div>
            </div>


            <div class="login-subtitle">Access the hospital management dashboard</div>

            <!-- Alert Messages -->
            @if (session('error'))
                <div class="alert alert-error" style="display:block">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" style="display:block">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif



            <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                @csrf
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                </div>

                <div class="role-selector">
                    <label for="role">Select Role</label>
                    <select id="role" name="user_type" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="admin">Administrator</option>
                        <option value="doctor">Doctor</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <div class="forgot-password">
                        <a href="#" id="forgotPassword">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>

                <div class="session-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Your session will expire after 30 minutes of inactivity</span>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
