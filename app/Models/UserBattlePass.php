<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBattlePass extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'username',
        'season_id',
        'current_level',
        'current_exp',
        'has_premium',
        'premium_purchased_at',
        'last_activity'
    ];

    protected $casts = [
        'has_premium' => 'boolean',
        'premium_purchased_at' => 'datetime',
        'last_activity' => 'datetime'
    ];

    public function season()
    {
        return $this->belongsTo(BattlePassSeason::class, 'season_id');
    }

    public function claims()
    {
        return $this->hasMany(BattlePassClaim::class, 'user_battle_pass_id');
    }

    public function getProgressPercentage()
    {
        if (!$this->season) return 0;
        
        return ($this->current_level / $this->season->max_level) * 100;
    }

    public function getStatusText()
    {
        return $this->has_premium ? 'Premium' : 'Free';
    }

    public function getStatusClass()
    {
        return $this->has_premium ? 'status-premium' : 'status-free';
    }

    public function canClaimReward($level, $isPremium = false)
    {
        if ($this->current_level < $level) {
            return false;
        }
        
        if ($isPremium && !$this->has_premium) {
            return false;
        }
        
        // Check if already claimed
        $claimed = $this->claims()
            ->whereHas('reward', function($query) use ($level, $isPremium) {
                $query->where('level', $level)
                      ->where('is_premium', $isPremium);
            })
            ->exists();
            
        return !$claimed;
    }
}
