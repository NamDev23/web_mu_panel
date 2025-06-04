<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class Account extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mysql';
    protected $table = 't_account';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'UserName',
        'Password',
        'Email',
        'CreateTime',
        'LastLoginTime',
        'Status',
        'DeviceID',
        'Session'
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    protected $casts = [
        'Status' => 'integer',
        'CreateTime' => 'datetime',
        'LastLoginTime' => 'datetime',
    ];

    // Relationships
    public function roles()
    {
        return $this->hasMany(Role::class, 'userid', 'ID');
    }

    // Helper methods
    public function isActive()
    {
        return $this->Status == 1; // Status 1 = active
    }

    public function isBanned()
    {
        return $this->Status == 0; // Status 0 = banned
    }

    public function isAdmin()
    {
        return false; // No admin system in this table
    }

    public function getStatusText()
    {
        return $this->Status == 1 ? 'Hoạt động' : 'Bị khóa';
    }

    public function getStatusBadgeClass()
    {
        return $this->Status == 1 ? 'status-active' : 'status-banned';
    }

    public function getFormattedMoney()
    {
        return number_format($this->Money);
    }

    // Override the password field name for authentication
    public function getAuthPassword()
    {
        return $this->Password;
    }

    // Override username field for authentication
    public function getAuthIdentifierName()
    {
        return 'UserName';
    }

    public function getAuthIdentifier()
    {
        return $this->UserName;
    }

    /**
     * Get game userid format (ZT + padded ID)
     */
    public function getGameUserId()
    {
        return 'ZT' . str_pad($this->ID, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get characters from game database
     */
    public function getGameCharacters()
    {
        return DB::connection('game_mysql')
            ->table('t_roles')
            ->where('userid', $this->getGameUserId())
            ->where('isdel', 0) // Only active characters
            ->select('rid', 'rname', 'userid')
            ->get();
    }

    /**
     * Get game money/coins
     */
    public function getGameMoney()
    {
        return DB::connection('game_mysql')
            ->table('t_money')
            ->where('userid', $this->getGameUserId())
            ->first();
    }

    /**
     * Get web coins balance
     */
    public function getWebCoins()
    {
        return DB::table('t_web_coins')
            ->where('account_id', $this->ID)
            ->first();
    }
}
