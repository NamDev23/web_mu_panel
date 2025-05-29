<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftcodeUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'giftcode_id',
        'giftcode',
        'giftcode_name',
        'status',
        'rewards_received',
        'error_message',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'rewards_received' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    public function giftcodeRecord()
    {
        return $this->belongsTo(Giftcode::class, 'giftcode_id');
    }

    // Helper methods
    public function getStatusText()
    {
        $statuses = [
            'success' => 'Thành công',
            'failed' => 'Thất bại',
            'expired' => 'Hết hạn',
            'used' => 'Đã sử dụng',
            'invalid' => 'Không hợp lệ'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'success':
                return 'status-completed';
            case 'failed':
            case 'expired':
            case 'used':
            case 'invalid':
                return 'status-rejected';
            default:
                return 'status-unknown';
        }
    }

    public function getStatusIcon()
    {
        switch ($this->status) {
            case 'success':
                return '✅';
            case 'failed':
                return '❌';
            case 'expired':
                return '⏰';
            case 'used':
                return '🔄';
            case 'invalid':
                return '❓';
            default:
                return '📝';
        }
    }

    public function isSuccess()
    {
        return $this->status === 'success';
    }

    public function getFormattedRewards()
    {
        if (!$this->rewards_received || !is_array($this->rewards_received)) {
            return 'Không có phần thưởng';
        }

        return implode(', ', $this->rewards_received);
    }

    // Static methods for creating logs
    public static function logSuccess($userId, $giftcode, $giftcodeName, $rewards, $giftcodeId = null)
    {
        return self::create([
            'user_id' => $userId,
            'giftcode_id' => $giftcodeId,
            'giftcode' => $giftcode,
            'giftcode_name' => $giftcodeName,
            'status' => 'success',
            'rewards_received' => $rewards,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function logFailure($userId, $giftcode, $errorMessage, $status = 'failed')
    {
        return self::create([
            'user_id' => $userId,
            'giftcode' => $giftcode,
            'status' => $status,
            'error_message' => $errorMessage,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'expired', 'used', 'invalid']);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
