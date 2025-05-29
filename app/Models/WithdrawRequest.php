<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_account_id',
        'game_username',
        'amount',
        'status',
        'web_coins_before',
        'web_coins_after',
        'game_coins_before',
        'game_coins_after',
        'exchange_rate',
        'notes',
        'error_message',
        'processed_by',
        'processed_at',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'amount' => 'integer',
        'web_coins_before' => 'integer',
        'web_coins_after' => 'integer',
        'game_coins_before' => 'integer',
        'game_coins_after' => 'integer',
        'exchange_rate' => 'decimal:4',
        'processed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    public function gameAccount()
    {
        return $this->belongsTo(GameAccount::class, 'game_account_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\DB::table('admin_users'), 'processed_by');
    }

    // Helper methods
    public function getStatusText()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'pending':
                return 'status-pending';
            case 'processing':
                return 'status-processing';
            case 'completed':
                return 'status-completed';
            case 'failed':
            case 'cancelled':
                return 'status-rejected';
            default:
                return 'status-unknown';
        }
    }

    public function getStatusIcon()
    {
        switch ($this->status) {
            case 'pending':
                return '⏳';
            case 'processing':
                return '🔄';
            case 'completed':
                return '✅';
            case 'failed':
                return '❌';
            case 'cancelled':
                return '🚫';
            default:
                return '❓';
        }
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    public function getFormattedAmount()
    {
        return number_format($this->amount);
    }

    public function getWebCoinChange()
    {
        if ($this->web_coins_after !== null) {
            return $this->web_coins_after - $this->web_coins_before;
        }
        return 0;
    }

    public function getGameCoinChange()
    {
        if ($this->game_coins_after !== null) {
            return $this->game_coins_after - $this->game_coins_before;
        }
        return 0;
    }

    public function getProcessingTime()
    {
        if ($this->processed_at) {
            return $this->created_at->diffForHumans($this->processed_at);
        }
        return null;
    }

    // Mark as completed
    public function complete($webCoinsAfter, $gameCoinsAfter, $notes = null, $processedBy = null)
    {
        $this->update([
            'status' => 'completed',
            'web_coins_after' => $webCoinsAfter,
            'game_coins_after' => $gameCoinsAfter,
            'notes' => $notes,
            'processed_by' => $processedBy,
            'processed_at' => now()
        ]);

        return $this;
    }

    // Mark as failed
    public function fail($errorMessage, $processedBy = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'processed_by' => $processedBy,
            'processed_at' => now()
        ]);

        return $this;
    }

    // Cancel request
    public function cancel($reason = null, $processedBy = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
            'processed_by' => $processedBy,
            'processed_at' => now()
        ]);

        return $this;
    }

    // Static methods
    public static function getDailyLimit()
    {
        return 500000; // 500,000 coins per day
    }

    public static function getMinAmount()
    {
        return 1000; // Minimum 1,000 coins
    }

    public static function getMaxAmount()
    {
        return 1000000; // Maximum 1,000,000 coins
    }

    public static function getUserDailyTotal($userId, $date = null)
    {
        $date = $date ?: today();
        
        return self::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('created_at', $date)
            ->sum('amount');
    }

    public static function getUserRemainingDaily($userId, $date = null)
    {
        $dailyTotal = self::getUserDailyTotal($userId, $date);
        return max(0, self::getDailyLimit() - $dailyTotal);
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }
}
