<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'character_id',
        'character_name',
        'service_type',
        'cost_coins',
        'status',
        'service_data',
        'before_data',
        'after_data',
        'notes',
        'error_message',
        'processed_by',
        'processed_at',
        'ip_address'
    ];

    protected $casts = [
        'cost_coins' => 'integer',
        'service_data' => 'array',
        'before_data' => 'array',
        'after_data' => 'array',
        'processed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(\DB::table('admin_users'), 'processed_by');
    }

    // Helper methods
    public function getServiceTypeText()
    {
        $types = [
            'rename' => 'Đổi tên nhân vật',
            'reset_stats' => 'Reset điểm kỹ năng',
            'reset_skills' => 'Reset skill',
            'change_class' => 'Đổi class',
            'teleport' => 'Dịch chuyển',
            'unbug' => 'Sửa lỗi nhân vật',
            'item_recovery' => 'Khôi phục item',
            'level_adjustment' => 'Điều chỉnh level'
        ];

        return $types[$this->service_type] ?? $this->service_type;
    }

    public function getServiceTypeIcon()
    {
        $icons = [
            'rename' => '✏️',
            'reset_stats' => '🔄',
            'reset_skills' => '🎯',
            'change_class' => '🔄',
            'teleport' => '🌀',
            'unbug' => '🔧',
            'item_recovery' => '📦',
            'level_adjustment' => '📈'
        ];

        return $icons[$this->service_type] ?? '⚙️';
    }

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClass()
    {
        switch ($this->status) {
            case 'pending':
                return 'status-pending';
            case 'processing':
                return 'status-processing';
            case 'completed':
                return 'status-completed';
            case 'failed':
                return 'status-rejected';
            case 'cancelled':
                return 'status-rejected';
            default:
                return 'status-unknown';
        }
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    // Static methods for creating service logs
    public static function createRenameRequest($userId, $characterId, $characterName, $newName, $cost)
    {
        return self::create([
            'user_id' => $userId,
            'character_id' => $characterId,
            'character_name' => $characterName,
            'service_type' => 'rename',
            'cost_coins' => $cost,
            'status' => 'pending',
            'service_data' => [
                'old_name' => $characterName,
                'new_name' => $newName
            ],
            'before_data' => [
                'name' => $characterName
            ],
            'ip_address' => request()->ip()
        ]);
    }

    public static function createResetStatsRequest($userId, $characterId, $characterName, $cost)
    {
        return self::create([
            'user_id' => $userId,
            'character_id' => $characterId,
            'character_name' => $characterName,
            'service_type' => 'reset_stats',
            'cost_coins' => $cost,
            'status' => 'pending',
            'service_data' => [
                'reset_type' => 'stats'
            ],
            'ip_address' => request()->ip()
        ]);
    }

    // Complete service
    public function complete($afterData = [], $notes = null, $processedBy = null)
    {
        $this->update([
            'status' => 'completed',
            'after_data' => $afterData,
            'notes' => $notes,
            'processed_by' => $processedBy,
            'processed_at' => now()
        ]);

        return $this;
    }

    // Fail service
    public function fail($errorMessage, $processedBy = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'processed_by' => $processedBy,
            'processed_at' => now()
        ]);

        return $this;
    }

    // Get service costs
    public static function getServiceCosts()
    {
        return [
            'rename' => 50000,
            'reset_stats' => 30000,
            'reset_skills' => 40000,
            'change_class' => 100000,
            'teleport' => 10000,
            'unbug' => 20000,
            'item_recovery' => 50000,
            'level_adjustment' => 80000
        ];
    }

    public static function getServiceCost($serviceType)
    {
        $costs = self::getServiceCosts();
        return $costs[$serviceType] ?? 0;
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByServiceType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
