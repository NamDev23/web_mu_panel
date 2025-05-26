<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MU Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            color: white;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: white;
            font-size: 14px;
        }

        .form-input {
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(16px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }

        .login-btn {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #10b981;
            color: #10b981;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px;
            }

            .login-title {
                font-size: 24px;
            }

            .login-icon {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="login-icon">⚔️</div>
            <h1 class="login-title">MU ADMIN PANEL</h1>
            <p class="login-subtitle">Game Management System</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('admin.login.post') }}" method="POST" class="login-form" id="loginForm">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">👤 Tên đăng nhập</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="Nhập tên đăng nhập"
                    value="{{ old('username') }}"
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">🔒 Mật khẩu</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Nhập mật khẩu"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="login-btn" id="loginBtn">
                🚀 Đăng nhập
            </button>
        </form>

        <!-- Footer -->
        <div class="footer-text">
            © 2024 MU Admin Panel. All rights reserved.
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.textContent = '⏳ Đang đăng nhập...';
        });

        // Auto focus on username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
