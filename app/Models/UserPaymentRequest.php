<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method',
        'amount',
        'coins_requested',
        'status',
        'transaction_ref',
        'proof_image',
        'qr_code_data',
        'admin_notes',
        'processed_by',
        'processed_at',
        'card_details',
        'gateway_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'coins_requested' => 'integer',
        'processed_at' => 'datetime',
        'card_details' => 'array',
        'qr_code_data' => 'array'
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

    // Status methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
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
            case 'rejected':
                return 'status-rejected';
            default:
                return 'status-unknown';
        }
    }

    public function getStatusText()
    {
        switch ($this->status) {
            case 'pending':
                return 'Chờ xử lý';
            case 'processing':
                return 'Đang xử lý';
            case 'completed':
                return 'Hoàn thành';
            case 'rejected':
                return 'Từ chối';
            default:
                return 'Không xác định';
        }
    }

    public function getPaymentMethodText()
    {
        switch ($this->payment_method) {
            case 'card':
                return 'Thẻ cào';
            case 'bank_transfer':
                return 'Chuyển khoản';
            case 'paypal':
                return 'PayPal';
            case 'crypto':
                return 'Cryptocurrency';
            default:
                return 'Khác';
        }
    }

    public function getPaymentMethodIcon()
    {
        switch ($this->payment_method) {
            case 'card':
                return '💳';
            case 'bank_transfer':
                return '🏦';
            case 'paypal':
                return '💰';
            case 'crypto':
                return '₿';
            default:
                return '💵';
        }
    }

    // Generate QR code data for bank transfer
    public static function generateBankQRData($amount, $userId, $requestId)
    {
        $transferContent = "NAPGAME {$userId} {$requestId}";
        
        return [
            'bank_name' => 'Vietcombank',
            'account_number' => '1234567890',
            'account_name' => 'GAME MU ONLINE',
            'amount' => $amount,
            'content' => $transferContent,
            'qr_string' => "VCB|1234567890|GAME MU ONLINE|{$amount}|{$transferContent}"
        ];
    }

    // Calculate coins based on amount and current exchange rate
    public static function calculateCoins($amount)
    {
        // Exchange rate: 1000 VND = 100 coins (can be configurable)
        $exchangeRate = config('game.coin_exchange_rate', 0.1);
        return intval($amount * $exchangeRate);
    }

    // Get card types for dropdown
    public static function getCardTypes()
    {
        return [
            'viettel' => 'Viettel',
            'mobifone' => 'Mobifone', 
            'vinaphone' => 'Vinaphone',
            'vietnamobile' => 'Vietnamobile',
            'gmobile' => 'Gmobile',
            'zing' => 'Zing Card',
            'gate' => 'Gate Card',
            'vcoin' => 'VCoin'
        ];
    }

    // Get card denominations
    public static function getCardDenominations()
    {
        return [
            10000 => '10,000 VND',
            20000 => '20,000 VND',
            30000 => '30,000 VND',
            50000 => '50,000 VND',
            100000 => '100,000 VND',
            200000 => '200,000 VND',
            300000 => '300,000 VND',
            500000 => '500,000 VND',
            1000000 => '1,000,000 VND'
        ];
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for filtering by payment method
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Scope for filtering by date range
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
