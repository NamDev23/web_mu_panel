<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin_users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Custom accessor for permissions
    public function getPermissionsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    // Custom mutator for permissions
    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = is_array($value) ? json_encode($value) : $value;
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(AdminUser::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(AdminUser::class, 'updated_by');
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function hasPermission($permission)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function getRoleText()
    {
        switch ($this->role) {
            case 'super_admin':
                return 'Super Admin';
            case 'admin':
                return 'Admin';
            case 'moderator':
                return 'Moderator';
            default:
                return 'Unknown';
        }
    }

    public function getStatusText()
    {
        return $this->is_active ? 'Hoạt động' : 'Bị khóa';
    }

    public function getStatusBadgeClass()
    {
        return $this->is_active ? 'status-active' : 'status-banned';
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Static methods
    public static function createAdmin($data)
    {
        return self::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'full_name' => $data['full_name'] ?? '',
            'role' => $data['role'] ?? 'admin',
            'permissions' => $data['permissions'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'created_by' => $data['created_by'] ?? null,
        ]);
    }

    public static function getDefaultPermissions()
    {
        return [
            'view_accounts',
            'edit_accounts',
            'ban_accounts',
            'view_characters',
            'edit_characters',
            'delete_characters',
            'manage_giftcodes',
            'manage_coins',
            'view_analytics',
            'manage_ip_bans',
            'view_logs',
        ];
    }

    public static function getSuperAdminPermissions()
    {
        return array_merge(self::getDefaultPermissions(), [
            'manage_admins',
            'system_settings',
            'database_access',
        ]);
    }
}
