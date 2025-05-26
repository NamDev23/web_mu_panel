<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    use HasFactory;

    protected $table = 'recharge_logs';

    protected $fillable = [
        'account_id',
        'username',
        'character_name',
        'amount',
        'coins_added',
        'type',
        'status',
        'transaction_id',
        'payment_method',
        'admin_id',
        'admin_username',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'coins_added' => 'integer'
    ];

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'completed':
                return 'status-completed';
            case 'pending':
                return 'status-pending';
            case 'failed':
                return 'status-failed';
            default:
                return 'status-unknown';
        }
    }

    public function getTypeBadgeClass()
    {
        switch ($this->type) {
            case 'manual':
                return 'type-manual';
            case 'automatic':
                return 'type-automatic';
            default:
                return 'type-unknown';
        }
    }
}
