<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quên mật khẩu - MU Game Portal</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
            padding: 1rem;
        }

        .forgot-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .logo p {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .input-group .form-input {
            padding-left: 2.5rem;
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: 1;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1rem;
            position: relative;
            z-index: 2;
        }

        .links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 0.875rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-box h4 {
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .info-box p {
            color: #6b7280;
            font-size: 0.75rem;
            line-height: 1.5;
        }

        /* Loading state */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .loading {
            display: none;
        }

        .btn:disabled .loading {
            display: inline-block;
        }

        .btn:disabled .btn-text {
            display: none;
        }

        @media (max-width: 480px) {
            .forgot-container {
                padding: 2rem;
                margin: 1rem;
            }

            .logo h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="logo">
            <h1>🎮 MU Game Portal</h1>
            <p>Khôi phục mật khẩu tài khoản</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="info-box">
            <h4><i class="fas fa-info-circle"></i> Thông báo</h4>
            <p>Chức năng đặt lại mật khẩu tự động đang được phát triển. Hiện tại vui lòng liên hệ admin để được hỗ trợ khôi phục mật khẩu.</p>
        </div>

        <form method="POST" action="{{ route('user.forgot-password.post') }}" id="forgotForm">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email đăng ký</label>
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        value="{{ old('email') }}"
                        placeholder="Nhập email đã đăng ký"
                        required
                        autocomplete="email"
                    >
                </div>
                @error('email')
                    <div style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" id="forgotBtn">
                <i class="fas fa-spinner fa-spin loading"></i>
                <span class="btn-text">
                    <i class="fas fa-paper-plane"></i>
                    Gửi yêu cầu
                </span>
            </button>
        </form>

        <div class="divider">
            <span>hoặc</span>
        </div>

        <div class="links">
            <p>Nhớ mật khẩu? <a href="{{ route('user.login') }}">Đăng nhập ngay</a></p>
            <p style="margin-top: 0.5rem;">Chưa có tài khoản? <a href="{{ route('user.register') }}">Đăng ký</a></p>
        </div>

        <div style="background: #fef3c7; border: 1px solid #fed7aa; border-radius: 12px; padding: 1rem; margin-top: 1.5rem;">
            <div style="font-weight: 500; color: #92400e; margin-bottom: 0.5rem;">
                <i class="fas fa-headset"></i> Hỗ trợ khẩn cấp
            </div>
            <div style="color: #92400e; font-size: 0.875rem;">
                <p>Liên hệ admin qua:</p>
                <p>• Email: admin@mugame.com</p>
                <p>• Telegram: @mugame_admin</p>
                <p>• Hotline: 1900-xxxx</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('forgotForm').addEventListener('submit', function() {
            const btn = document.getElementById('forgotBtn');
            btn.disabled = true;
        });

        // Auto focus on email input
        document.getElementById('email').focus();
    </script>
</body>
</html>
