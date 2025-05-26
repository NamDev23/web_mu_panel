<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giftcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'rewards',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
        'admin_id',
        'admin_username'
    ];

    protected $casts = [
        'rewards' => 'array',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function usages()
    {
        return $this->hasMany(GiftcodeUsage::class);
    }

    public function isExpired()
    {
        return $this->expires_at && now() > $this->expires_at;
    }

    public function isUsedUp()
    {
        return $this->used_count >= $this->max_uses;
    }

    public function canBeUsed()
    {
        return $this->is_active && !$this->isExpired() && !$this->isUsedUp();
    }

    public function getUsagePercentage()
    {
        if ($this->max_uses == 0) return 0;
        return ($this->used_count / $this->max_uses) * 100;
    }
}
