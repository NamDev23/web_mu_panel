<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $table = 'ip_logs';

    protected $fillable = [
        'account_id',
        'username',
        'ip_address',
        'action',
        'user_agent',
        'location',
        'is_suspicious',
        'created_at'
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
        'created_at' => 'datetime'
    ];

    public function getActionBadgeClass()
    {
        switch ($this->action) {
            case 'login':
                return 'action-login';
            case 'logout':
                return 'action-logout';
            case 'register':
                return 'action-register';
            case 'failed_login':
                return 'action-failed';
            default:
                return 'action-unknown';
        }
    }

    public function getActionText()
    {
        switch ($this->action) {
            case 'login':
                return 'Đăng nhập';
            case 'logout':
                return 'Đăng xuất';
            case 'register':
                return 'Đăng ký';
            case 'failed_login':
                return 'Đăng nhập thất bại';
            default:
                return ucfirst($this->action);
        }
    }

    public function getSuspiciousText()
    {
        return $this->is_suspicious ? 'Đáng nghi' : 'Bình thường';
    }

    public function getSuspiciousClass()
    {
        return $this->is_suspicious ? 'suspicious-yes' : 'suspicious-no';
    }
}
