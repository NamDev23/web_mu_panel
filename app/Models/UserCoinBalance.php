<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoinBalance extends Model
{
    use HasFactory;

    protected $table = 'user_coin_balance';

    protected $fillable = [
        'user_id',
        'web_coins',
        'game_coins',
        'total_recharged',
        'last_recharge_at'
    ];

    protected $casts = [
        'web_coins' => 'integer',
        'game_coins' => 'integer',
        'total_recharged' => 'decimal:2',
        'last_recharge_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    // Helper methods
    public function getTotalCoins()
    {
        return $this->web_coins + $this->game_coins;
    }

    public function getFormattedWebCoins()
    {
        return number_format($this->web_coins);
    }

    public function getFormattedGameCoins()
    {
        return number_format($this->game_coins);
    }

    public function getFormattedTotalRecharged()
    {
        return number_format($this->total_recharged);
    }

    // Transfer coins from web to game
    public function transferToGame($amount)
    {
        if ($this->web_coins < $amount) {
            throw new \Exception('Insufficient web coins');
        }

        $this->web_coins -= $amount;
        $this->game_coins += $amount;
        $this->save();

        return $this;
    }

    // Add coins from recharge
    public function addFromRecharge($amount, $rechargeAmount = null)
    {
        $this->web_coins += $amount;
        
        if ($rechargeAmount) {
            $this->total_recharged += $rechargeAmount;
        }
        
        $this->last_recharge_at = now();
        $this->save();

        return $this;
    }

    // Deduct coins for services
    public function deductForService($amount, $serviceName = null)
    {
        if ($this->web_coins < $amount) {
            throw new \Exception("Insufficient coins for service: {$serviceName}");
        }

        $this->web_coins -= $amount;
        $this->save();

        return $this;
    }
}
