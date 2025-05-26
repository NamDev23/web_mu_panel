<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattlePassClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_battle_pass_id',
        'season_id',
        'reward_id',
        'account_id',
        'username',
        'level',
        'reward_type',
        'reward_data',
        'claimed_at'
    ];

    protected $casts = [
        'reward_data' => 'array',
        'claimed_at' => 'datetime'
    ];

    public function userBattlePass()
    {
        return $this->belongsTo(UserBattlePass::class, 'user_battle_pass_id');
    }

    public function season()
    {
        return $this->belongsTo(BattlePassSeason::class, 'season_id');
    }

    public function reward()
    {
        return $this->belongsTo(BattlePassReward::class, 'reward_id');
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
}
