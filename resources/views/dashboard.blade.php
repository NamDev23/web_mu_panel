@extends('layouts.admin')

@section('title', 'Dashboard - MU Admin Panel')

@section('content')
    <div class="content-card">
        <h1 style="text-align: center; margin-bottom: 30px; font-size: 28px;">🎮 Dashboard - MU Admin Panel</h1>
        <p style="text-align: center; margin-bottom: 40px; opacity: 0.8;">Tổng quan hệ thống quản lý game MU Online</p>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="content-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 48px; margin-bottom: 15px;">👥</div>
                <div style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">{{ number_format($stats['accounts'] ?? 0) }}</div>
                <div style="opacity: 0.8; font-size: 14px;">Tài khoản</div>
            </div>

            <div class="content-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 48px; margin-bottom: 15px;">⚔️</div>
                <div style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">{{ number_format($stats['characters'] ?? 0) }}</div>
                <div style="opacity: 0.8; font-size: 14px;">Nhân vật</div>
            </div>

            <div class="content-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 48px; margin-bottom: 15px;">🎁</div>
                <div style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">{{ number_format($stats['giftcodes'] ?? 0) }}</div>
                <div style="opacity: 0.8; font-size: 14px;">Giftcode</div>
            </div>

            <div class="content-card" style="text-align: center; padding: 25px;">
                <div style="font-size: 48px; margin-bottom: 15px;">💰</div>
                <div style="font-size: 28px; font-weight: 700; margin-bottom: 5px;">{{ number_format($stats['total_coins'] ?? 0) }}</div>
                <div style="opacity: 0.8; font-size: 14px;">Tổng Coin</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-card">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            ⚡ Thao tác nhanh
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="/admin/admin-users" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">👨‍💼</span>
                <span style="font-weight: 500;">Quản lý Admin</span>
            </a>

            <a href="/admin/accounts" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">👥</span>
                <span style="font-weight: 500;">Quản lý tài khoản</span>
            </a>

            <a href="/admin/characters" class="btn btn-success" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">⚔️</span>
                <span style="font-weight: 500;">Quản lý nhân vật</span>
            </a>

            <a href="/admin/coin-recharge" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">💰</span>
                <span style="font-weight: 500;">Nạp coin</span>
            </a>

            <a href="/admin/giftcodes" class="btn btn-secondary" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">🎁</span>
                <span style="font-weight: 500;">Quản lý giftcode</span>
            </a>

            <a href="/admin/analytics" class="btn btn-success" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">📊</span>
                <span style="font-weight: 500;">Analytics</span>
            </a>

            <a href="/admin/ip-management" class="btn btn-danger" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">🛡️</span>
                <span style="font-weight: 500;">IP Management</span>
            </a>

            <a href="/admin/logs" class="btn btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">📝</span>
                <span style="font-weight: 500;">Admin Logs</span>
            </a>

            <a href="/admin/monthly-cards" class="btn btn-success" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">🎫</span>
                <span style="font-weight: 500;">Monthly Cards</span>
            </a>

            <a href="/admin/battle-pass" class="btn btn-secondary" style="display: flex; align-items: center; gap: 10px; padding: 15px 20px; text-decoration: none;">
                <span style="font-size: 20px;">🏆</span>
                <span style="font-weight: 500;">Battle Pass</span>
            </a>
        </div>
    </div>
@endsection
