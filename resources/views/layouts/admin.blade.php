<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MU Admin Panel')</title>
    @yield('head')
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
            color: white;
        }
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .nav-links a:hover, .nav-links a.active {
            background: rgba(255, 255, 255, 0.1);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }
        .content-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
            color: white;
        }
        td {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.15) !important;
        }
        tr:hover td {
            background: rgba(255, 255, 255, 0.15) !important;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: white;
        }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(16px);
        }
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid #10b981;
            color: #10b981;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid #ef4444;
            color: #ef4444;
        }
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            .header {
                padding: 10px 15px;
            }
            .nav-links {
                gap: 10px;
            }
            .nav-links a {
                padding: 6px 12px;
                font-size: 14px;
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🎮 MU ADMIN PANEL</h1>
        <div class="nav-links">
            <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="/admin/admin-users" class="{{ request()->is('admin/admin-users*') ? 'active' : '' }}">Admin Users</a>
            <a href="/admin/accounts" class="{{ request()->is('admin/accounts*') ? 'active' : '' }}">Tài khoản</a>
            <a href="/admin/characters" class="{{ request()->is('admin/characters*') ? 'active' : '' }}">Nhân vật</a>
            <a href="/admin/game-money" class="{{ request()->is('admin/game-money*') ? 'active' : '' }}">Xu Game</a>
            <a href="/admin/coin-recharge" class="{{ request()->is('admin/coin-recharge*') ? 'active' : '' }}">Nạp Coin</a>
            <a href="/admin/giftcodes" class="{{ request()->is('admin/giftcodes*') ? 'active' : '' }}">Giftcode</a>
            <a href="/admin/analytics" class="{{ request()->is('admin/analytics*') ? 'active' : '' }}">Analytics</a>
            <a href="/admin/ip-management" class="{{ request()->is('admin/ip-management*') ? 'active' : '' }}">IP Management</a>
            <a href="/admin/system/performance" class="{{ request()->is('admin/system*') ? 'active' : '' }}">System</a>
            <a href="/admin/monthly-cards" class="{{ request()->is('admin/monthly-cards*') ? 'active' : '' }}">Monthly Cards</a>
            <a href="/admin/battle-pass" class="{{ request()->is('admin/battle-pass*') ? 'active' : '' }}">Battle Pass</a>
            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Đăng xuất</button>
            </form>
        </div>
    </div>

    <main class="container">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>