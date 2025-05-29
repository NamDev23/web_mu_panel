<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameAccount extends Model
{
    use HasFactory;

    protected $table = 'game_accounts';

    protected $fillable = [
        'username',
        'email',
        'current_balance',
        'total_recharge',
        'status',
        'last_login_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'current_balance' => 'integer',
        'total_recharge' => 'decimal:2',
        'last_login_at' => 'datetime'
    ];

    // Relationships
    public function userAccount()
    {
        return $this->hasOne(UserAccount::class, 'game_account_id');
    }

    public function characters()
    {
        return $this->hasMany(Character::class, 'userid', 'id');
    }

    public function recharges()
    {
        return $this->hasMany(Recharge::class, 'account_id');
    }

    public function monthlyCards()
    {
        return $this->hasMany(MonthlyCard::class, 'username', 'username');
    }

    public function battlePass()
    {
        return $this->hasOne(UserBattlePass::class, 'account_id');
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

    public function getFormattedBalance()
    {
        return number_format($this->current_balance);
    }

    public function getFormattedTotalRecharge()
    {
        return number_format($this->total_recharge);
    }

    // Add coins to game account
    public function addCoins($amount, $reason = null)
    {
        $this->current_balance += $amount;
        $this->save();

        // In real implementation, this would also update the actual game database
        // For now, we just update our local copy

        return $this;
    }

    // Deduct coins from game account
    public function deductCoins($amount, $reason = null)
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('Insufficient game coins');
        }

        $this->current_balance -= $amount;
        $this->save();

        return $this;
    }
}
