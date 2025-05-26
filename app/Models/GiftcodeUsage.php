<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftcodeUsage extends Model
{
    use HasFactory;

    protected $table = 'giftcode_usage';

    protected $fillable = [
        'giftcode_id',
        'account_id',
        'username',
        'character_name',
        'rewards_received',
        'ip_address',
        'used_at'
    ];

    protected $casts = [
        'rewards_received' => 'array',
        'used_at' => 'datetime'
    ];

    public function giftcode()
    {
        return $this->belongsTo(Giftcode::class);
    }
}
