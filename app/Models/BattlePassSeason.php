<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattlePassSeason extends Model
{
    use HasFactory;

    protected $table = 'battle_pass_seasons';

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'max_level',
        'premium_price',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'premium_price' => 'decimal:2'
    ];

    public function rewards()
    {
        return $this->hasMany(BattlePassReward::class, 'season_id');
    }

    public function userProgress()
    {
        return $this->hasMany(UserBattlePass::class, 'season_id');
    }

    public function claims()
    {
        return $this->hasMany(BattlePassClaim::class, 'season_id');
    }

    public function isActive()
    {
        return $this->is_active && 
               now() >= $this->start_date && 
               now() <= $this->end_date;
    }

    public function isExpired()
    {
        return now() > $this->end_date;
    }

    public function getStatusText()
    {
        if (!$this->is_active) {
            return 'Vô hiệu hóa';
        }
        
        if (now() < $this->start_date) {
            return 'Sắp bắt đầu';
        }
        
        if ($this->isExpired()) {
            return 'Đã kết thúc';
        }
        
        return 'Đang hoạt động';
    }

    public function getStatusClass()
    {
        if (!$this->is_active) {
            return 'status-inactive';
        }
        
        if (now() < $this->start_date) {
            return 'status-upcoming';
        }
        
        if ($this->isExpired()) {
            return 'status-expired';
        }
        
        return 'status-active';
    }
}
