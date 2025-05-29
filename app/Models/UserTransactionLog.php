<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTransactionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'coin_amount',
        'coin_before',
        'coin_after',
        'metadata',
        'reference_type',
        'reference_id',
        'processed_by',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'coin_amount' => 'integer',
        'coin_before' => 'integer',
        'coin_after' => 'integer',
        'metadata' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\DB::table('admin_users'), 'processed_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Helper methods
    public function getTypeText()
    {
        $types = [
            'coin_add' => 'Cộng Coin',
            'coin_deduct' => 'Trừ Coin',
            'service_purchase' => 'Mua dịch vụ',
            'character_rename' => 'Đổi tên nhân vật',
            'character_reset' => 'Reset stats',
            'giftcode_redeem' => 'Nhập giftcode',
            'transfer_to_game' => 'Chuyển coin sang game',
            'admin_adjustment' => 'Admin điều chỉnh'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getTypeIcon()
    {
        $icons = [
            'coin_add' => '💰',
            'coin_deduct' => '💸',
            'service_purchase' => '🛒',
            'character_rename' => '✏️',
            'character_reset' => '🔄',
            'giftcode_redeem' => '🎁',
            'transfer_to_game' => '🎮',
            'admin_adjustment' => '⚙️'
        ];

        return $icons[$this->type] ?? '📝';
    }

    public function getAmountClass()
    {
        if ($this->coin_amount > 0) {
            return 'text-green-600';
        } elseif ($this->coin_amount < 0) {
            return 'text-red-600';
        }
        return 'text-gray-600';
    }

    public function getFormattedAmount()
    {
        if ($this->coin_amount > 0) {
            return '+' . number_format($this->coin_amount);
        }
        return number_format($this->coin_amount);
    }

    // Static methods for creating logs
    public static function logCoinAdd($userId, $amount, $description, $reference = null, $processedBy = null)
    {
        $user = UserAccount::find($userId);
        $coinBalance = $user->coinBalance;
        
        $coinBefore = $coinBalance ? $coinBalance->web_coins : 0;
        $coinAfter = $coinBefore + $amount;

        return self::create([
            'user_id' => $userId,
            'type' => 'coin_add',
            'description' => $description,
            'coin_amount' => $amount,
            'coin_before' => $coinBefore,
            'coin_after' => $coinAfter,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'processed_by' => $processedBy,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function logCoinDeduct($userId, $amount, $description, $reference = null)
    {
        $user = UserAccount::find($userId);
        $coinBalance = $user->coinBalance;
        
        $coinBefore = $coinBalance ? $coinBalance->web_coins : 0;
        $coinAfter = $coinBefore - $amount;

        return self::create([
            'user_id' => $userId,
            'type' => 'coin_deduct',
            'description' => $description,
            'coin_amount' => -$amount,
            'coin_before' => $coinBefore,
            'coin_after' => $coinAfter,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function logServicePurchase($userId, $serviceType, $cost, $metadata = [])
    {
        $user = UserAccount::find($userId);
        $coinBalance = $user->coinBalance;
        
        $coinBefore = $coinBalance ? $coinBalance->web_coins : 0;
        $coinAfter = $coinBefore - $cost;

        return self::create([
            'user_id' => $userId,
            'type' => 'service_purchase',
            'description' => "Mua dịch vụ: {$serviceType}",
            'coin_amount' => -$cost,
            'coin_before' => $coinBefore,
            'coin_after' => $coinAfter,
            'metadata' => array_merge(['service_type' => $serviceType], $metadata),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public static function logGiftcodeRedeem($userId, $giftcode, $rewards, $giftcodeId = null)
    {
        $totalCoins = 0;
        if (is_array($rewards)) {
            foreach ($rewards as $reward) {
                if (strpos($reward, 'Coin') !== false) {
                    preg_match('/(\d+)/', $reward, $matches);
                    if (!empty($matches)) {
                        $totalCoins += intval($matches[0]);
                    }
                }
            }
        }

        $user = UserAccount::find($userId);
        $coinBalance = $user->coinBalance;
        
        $coinBefore = $coinBalance ? $coinBalance->web_coins : 0;
        $coinAfter = $coinBefore + $totalCoins;

        return self::create([
            'user_id' => $userId,
            'type' => 'giftcode_redeem',
            'description' => "Nhập giftcode: {$giftcode}",
            'coin_amount' => $totalCoins,
            'coin_before' => $coinBefore,
            'coin_after' => $coinAfter,
            'metadata' => [
                'giftcode' => $giftcode,
                'giftcode_id' => $giftcodeId,
                'rewards' => $rewards
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeCoinChanges($query)
    {
        return $query->whereIn('type', ['coin_add', 'coin_deduct']);
    }
}
