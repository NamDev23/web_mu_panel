<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPBan extends Model
{
    use HasFactory;

    protected $table = 'banned_ips';

    protected $fillable = [
        'ip_address',
        'reason',
        'banned_by',
        'banned_at',
        'expires_at',
        'is_permanent',
        'is_active'
    ];

    protected $casts = [
        'banned_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_permanent' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function isExpired()
    {
        if ($this->is_permanent) {
            return false;
        }
        
        return $this->expires_at && now() > $this->expires_at;
    }

    public function isActive()
    {
        return $this->is_active && !$this->isExpired();
    }

    public function getStatusText()
    {
        if (!$this->is_active) {
            return 'Đã gỡ ban';
        }
        
        if ($this->is_permanent) {
            return 'Ban vĩnh viễn';
        }
        
        if ($this->isExpired()) {
            return 'Đã hết hạn';
        }
        
        return 'Đang bị ban';
    }

    public function getStatusClass()
    {
        if (!$this->is_active) {
            return 'status-unbanned';
        }
        
        if ($this->is_permanent) {
            return 'status-permanent';
        }
        
        if ($this->isExpired()) {
            return 'status-expired';
        }
        
        return 'status-active';
    }
}
