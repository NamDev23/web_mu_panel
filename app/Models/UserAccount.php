<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class UserAccount extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_accounts';

    protected $fillable = [
        'username',
        'email',
        'password',
        'phone',
        'game_account_id',
        'status',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'string'
    ];

    // Relationships
    public function gameAccount()
    {
        return $this->belongsTo(GameAccount::class, 'game_account_id');
    }

    public function paymentRequests()
    {
        return $this->hasMany(UserPaymentRequest::class, 'user_id');
    }

    public function coinBalance()
    {
        return $this->hasOne(UserCoinBalance::class, 'user_id');
    }

    public function giftcodeUsages()
    {
        return $this->hasMany(GiftcodeUsage::class, 'account_id', 'game_account_id');
    }

    public function transactionLogs()
    {
        return $this->hasMany(UserTransactionLog::class, 'user_id');
    }

    public function characterServiceLogs()
    {
        return $this->hasMany(CharacterServiceLog::class, 'user_id');
    }

    public function giftcodeUsageLogs()
    {
        return $this->hasMany(GiftcodeUsageLog::class, 'user_id');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isBanned()
    {
        return $this->status === 'banned';
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'active':
                return 'status-active';
            case 'banned':
                return 'status-banned';
            case 'suspended':
                return 'status-suspended';
            default:
                return 'status-unknown';
        }
    }

    public function getStatusText()
    {
        switch ($this->status) {
            case 'active':
                return 'Hoạt động';
            case 'banned':
                return 'Bị cấm';
            case 'suspended':
                return 'Tạm khóa';
            default:
                return 'Không xác định';
        }
    }

    // Get user's current coin balance
    public function getCurrentCoins()
    {
        $balance = $this->coinBalance;
        return $balance ? $balance->web_coins : 0;
    }

    // Get user's total recharged amount
    public function getTotalRecharged()
    {
        $balance = $this->coinBalance;
        return $balance ? $balance->total_recharged : 0;
    }

    // Check if user has enough coins for a service
    public function hasEnoughCoins($amount)
    {
        return $this->getCurrentCoins() >= $amount;
    }

    // Deduct coins from user balance with logging
    public function deductCoins($amount, $reason = null, $reference = null)
    {
        $balance = $this->coinBalance;
        if (!$balance) {
            throw new \Exception('User coin balance not found');
        }

        if ($balance->web_coins < $amount) {
            throw new \Exception('Insufficient coins');
        }

        // Log transaction before deducting
        UserTransactionLog::logCoinDeduct($this->id, $amount, $reason ?: 'Trừ coin', $reference);

        $balance->web_coins -= $amount;
        $balance->save();

        return $balance;
    }

    // Add coins to user balance with logging
    public function addCoins($amount, $reason = null, $fromRecharge = false, $reference = null, $processedBy = null)
    {
        $balance = $this->coinBalance;
        if (!$balance) {
            $balance = UserCoinBalance::create([
                'user_id' => $this->id,
                'web_coins' => 0,
                'game_coins' => 0,
                'total_recharged' => 0
            ]);
        }

        // Log transaction before adding
        UserTransactionLog::logCoinAdd($this->id, $amount, $reason ?: 'Cộng coin', $reference, $processedBy);

        $balance->web_coins += $amount;

        if ($fromRecharge) {
            $balance->total_recharged += $amount;
            $balance->last_recharge_at = now();
        }

        $balance->save();

        return $balance;
    }

    // Get user's monthly cards
    public function getMonthlyCards()
    {
        if (!$this->gameAccount) {
            return collect();
        }

        return \DB::table('monthly_cards')
            ->where('username', $this->gameAccount->username)
            ->where('type', 'monthly_card')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Get user's battle pass progress
    public function getBattlePassProgress()
    {
        if (!$this->gameAccount) {
            return null;
        }

        return UserBattlePass::where('account_id', $this->game_account_id)
            ->with('season')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    // Password mutator - only hash if not already hashed
    public function setPasswordAttribute($value)
    {
        // Check if password is already hashed (starts with $2y$)
        if (!str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
