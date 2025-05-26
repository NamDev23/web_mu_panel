<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattlePassReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'level',
        'reward_type',
        'reward_data',
        'is_premium',
        'created_by'
    ];

    protected $casts = [
        'reward_data' => 'array',
        'is_premium' => 'boolean'
    ];

    public function season()
    {
        return $this->belongsTo(BattlePassSeason::class, 'season_id');
    }

    public function claims()
    {
        return $this->hasMany(BattlePassClaim::class, 'reward_id');
    }

    public function getRewardDescription()
    {
        $data = $this->reward_data;
        
        switch ($this->reward_type) {
            case 'coins':
                return number_format($data['amount']) . ' Coins';
            case 'items':
                return $data['name'] . ' x' . $data['quantity'];
            case 'experience':
                return number_format($data['amount']) . ' EXP';
            default:
                return 'Unknown Reward';
        }
    }

    public function getTypeText()
    {
        return $this->is_premium ? 'Premium' : 'Free';
    }

    public function getTypeClass()
    {
        return $this->is_premium ? 'reward-premium' : 'reward-free';
    }
}
