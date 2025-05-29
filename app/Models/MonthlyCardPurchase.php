<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyCardPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_name',
        'package_type',
        'duration_days',
        'cost_coins',
        'daily_reward_coins',
        'bonus_items',
        'daily_items',
        'status',
        'activated_at',
        'expires_at',
        'last_claimed_at',
        'days_claimed',
        'notes',
        'ip_address'
    ];

    protected $casts = [
        'cost_coins' => 'integer',
        'daily_reward_coins' => 'decimal:2',
        'bonus_items' => 'array',
        'daily_items' => 'array',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_claimed_at' => 'datetime',
        'days_claimed' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    public function isExpired()
    {
        return $this->expires_at <= now() || $this->status === 'expired';
    }

    public function canClaimToday()
    {
        if (!$this->isActive()) {
            return false;
        }

        if (!$this->last_claimed_at) {
            return true;
        }

        return $this->last_claimed_at->format('Y-m-d') !== now()->format('Y-m-d');
    }

    public function getRemainingDays()
    {
        if ($this->isExpired()) {
            return 0;
        }

        return max(0, $this->expires_at->diffInDays(now()));
    }

    public function getProgressPercentage()
    {
        $totalDays = $this->duration_days;
        $remainingDays = $this->getRemainingDays();
        $usedDays = $totalDays - $remainingDays;
        
        return min(100, ($usedDays / $totalDays) * 100);
    }

    public function getStatusText()
    {
        switch ($this->status) {
            case 'active':
                return $this->isExpired() ? 'Hết hạn' : 'Đang hoạt động';
            case 'expired':
                return 'Hết hạn';
            case 'cancelled':
                return 'Đã hủy';
            default:
                return 'Không xác định';
        }
    }

    public function getStatusBadgeClass()
    {
        if ($this->isActive()) {
            return 'status-active';
        } elseif ($this->isExpired()) {
            return 'status-rejected';
        } else {
            return 'status-unknown';
        }
    }

    public function getPackageTypeText()
    {
        switch ($this->package_type) {
            case 'basic':
                return 'Cơ bản';
            case 'premium':
                return 'Cao cấp';
            case 'vip':
                return 'VIP';
            default:
                return 'Không xác định';
        }
    }

    public function getPackageTypeIcon()
    {
        switch ($this->package_type) {
            case 'basic':
                return '🥉';
            case 'premium':
                return '🥈';
            case 'vip':
                return '🥇';
            default:
                return '📦';
        }
    }

    // Static methods for package definitions
    public static function getAvailablePackages()
    {
        return [
            'basic_30' => [
                'name' => 'Thẻ Tháng Cơ Bản',
                'type' => 'basic',
                'duration_days' => 30,
                'cost_coins' => 100000,
                'daily_reward_coins' => 1000,
                'bonus_items' => ['5 Jewel of Bless', '10 Jewel of Soul'],
                'daily_items' => ['100 Coin', '1 Jewel of Bless'],
                'description' => 'Nhận 1,000 coin + items mỗi ngày trong 30 ngày'
            ],
            'premium_30' => [
                'name' => 'Thẻ Tháng Cao Cấp',
                'type' => 'premium',
                'duration_days' => 30,
                'cost_coins' => 200000,
                'daily_reward_coins' => 2000,
                'bonus_items' => ['10 Jewel of Bless', '20 Jewel of Soul', '1 Jewel of Life'],
                'daily_items' => ['200 Coin', '2 Jewel of Bless', '1 Jewel of Soul'],
                'description' => 'Nhận 2,000 coin + items mỗi ngày trong 30 ngày'
            ],
            'vip_30' => [
                'name' => 'Thẻ Tháng VIP',
                'type' => 'vip',
                'duration_days' => 30,
                'cost_coins' => 500000,
                'daily_reward_coins' => 5000,
                'bonus_items' => ['20 Jewel of Bless', '50 Jewel of Soul', '3 Jewel of Life', '1 Box of Luck'],
                'daily_items' => ['500 Coin', '5 Jewel of Bless', '2 Jewel of Soul'],
                'description' => 'Nhận 5,000 coin + items mỗi ngày trong 30 ngày'
            ]
        ];
    }

    public static function getPackageInfo($packageKey)
    {
        $packages = self::getAvailablePackages();
        return $packages[$packageKey] ?? null;
    }

    // Claim daily reward
    public function claimDailyReward()
    {
        if (!$this->canClaimToday()) {
            throw new \Exception('Không thể nhận thưởng hôm nay');
        }

        // Add coins to user
        $this->user->addCoins(
            $this->daily_reward_coins,
            "Nhận thưởng hàng ngày từ {$this->package_name}",
            false,
            $this
        );

        // Update claim status
        $this->update([
            'last_claimed_at' => now(),
            'days_claimed' => $this->days_claimed + 1
        ]);

        // Log transaction
        UserTransactionLog::create([
            'user_id' => $this->user_id,
            'type' => 'coin_add',
            'description' => "Nhận thưởng hàng ngày từ {$this->package_name}",
            'coin_amount' => $this->daily_reward_coins,
            'coin_before' => $this->user->getCurrentCoins() - $this->daily_reward_coins,
            'coin_after' => $this->user->getCurrentCoins(),
            'metadata' => [
                'monthly_card_id' => $this->id,
                'package_type' => $this->package_type,
                'daily_items' => $this->daily_items
            ],
            'reference_type' => self::class,
            'reference_id' => $this->id,
            'ip_address' => request()->ip()
        ]);

        return $this;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByPackageType($query, $type)
    {
        return $query->where('package_type', $type);
    }
}
