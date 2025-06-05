<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $role = $request->get('role');

        $query = AdminUser::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $adminUsers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.admin-users.index', compact('admin', 'adminUsers', 'search', 'role'));
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');
        $adminUser = AdminUser::with(['creator', 'updater'])->findOrFail($id);

        // Get recent login logs
        $recentLogins = DB::table('admin_action_logs')
            ->where('admin_id', $id)
            ->where('action', 'login')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.admin-users.show', compact('admin', 'adminUser', 'recentLogins'));
    }

    public function create()
    {
        $admin = Session::get('admin_user');
        
        // Only super admin can create new admin users
        if ($admin['role'] !== 'super_admin') {
            return redirect()->route('admin.admin-users.index')
                ->withErrors(['error' => 'Bạn không có quyền tạo admin user mới.']);
        }

        return view('admin.admin-users.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $admin = Session::get('admin_user');
        
        // Only super admin can create new admin users
        if ($admin['role'] !== 'super_admin') {
            return redirect()->route('admin.admin-users.index')
                ->withErrors(['error' => 'Bạn không có quyền tạo admin user mới.']);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:admin_users',
            'email' => 'required|email|max:100|unique:admin_users',
            'password' => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'role' => 'required|in:admin,moderator',
            'permissions' => 'array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $adminUser = AdminUser::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'full_name' => $request->full_name,
            'role' => $request->role,
            'permissions' => $request->permissions ?? [],
            'is_active' => $request->has('is_active'),
            'created_by' => $admin['id'],
        ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'create_admin_user',
            'admin_user',
            $adminUser->id,
            $adminUser->username,
            [],
            $adminUser->toArray(),
            'Tạo admin user mới',
            $request->ip()
        );

        return redirect()->route('admin.admin-users.show', $adminUser->id)
            ->with('success', "Đã tạo admin user {$adminUser->username} thành công.");
    }

    public function edit($id)
    {
        $admin = Session::get('admin_user');
        $adminUser = AdminUser::findOrFail($id);
        
        // Only super admin can edit admin users, or admin can edit themselves
        if ($admin['role'] !== 'super_admin' && $admin['id'] != $id) {
            return redirect()->route('admin.admin-users.index')
                ->withErrors(['error' => 'Bạn không có quyền chỉnh sửa admin user này.']);
        }

        return view('admin.admin-users.edit', compact('admin', 'adminUser'));
    }

    public function update(Request $request, $id)
    {
        $admin = Session::get('admin_user');
        $adminUser = AdminUser::findOrFail($id);
        
        // Only super admin can edit admin users, or admin can edit themselves
        if ($admin['role'] !== 'super_admin' && $admin['id'] != $id) {
            return redirect()->route('admin.admin-users.index')
                ->withErrors(['error' => 'Bạn không có quyền chỉnh sửa admin user này.']);
        }

        $rules = [
            'email' => 'required|email|max:100|unique:admin_users,email,' . $id,
            'full_name' => 'required|string|max:255',
            'permissions' => 'array',
        ];

        // Only super admin can change role and status
        if ($admin['role'] === 'super_admin') {
            $rules['role'] = 'required|in:super_admin,admin,moderator';
            $rules['is_active'] = 'boolean';
        }

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $adminUser->toArray();

        $updateData = [
            'email' => $request->email,
            'full_name' => $request->full_name,
            'permissions' => $request->permissions ?? [],
            'updated_by' => $admin['id'],
        ];

        // Only super admin can change role and status
        if ($admin['role'] === 'super_admin') {
            $updateData['role'] = $request->role;
            $updateData['is_active'] = $request->has('is_active');
        }

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $adminUser->update($updateData);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'update_admin_user',
            'admin_user',
            $adminUser->id,
            $adminUser->username,
            $oldData,
            $adminUser->fresh()->toArray(),
            'Cập nhật thông tin admin user',
            $request->ip()
        );

        return redirect()->route('admin.admin-users.show', $adminUser->id)
            ->with('success', "Đã cập nhật thông tin admin user {$adminUser->username} thành công.");
    }

    public function toggleStatus($id)
    {
        $admin = Session::get('admin_user');
        $adminUser = AdminUser::findOrFail($id);
        
        // Only super admin can toggle status
        if ($admin['role'] !== 'super_admin') {
            return redirect()->route('admin.admin-users.index')
                ->withErrors(['error' => 'Bạn không có quyền thay đổi trạng thái admin user này.']);
        }

        // Cannot disable yourself
        if ($admin['id'] == $id) {
            return redirect()->route('admin.admin-users.show', $id)
                ->withErrors(['error' => 'Bạn không thể vô hiệu hóa tài khoản của chính mình.']);
        }

        $oldStatus = $adminUser->is_active;
        $adminUser->update([
            'is_active' => !$adminUser->is_active,
            'updated_by' => $admin['id'],
        ]);

        $action = $adminUser->is_active ? 'activate_admin_user' : 'deactivate_admin_user';
        $message = $adminUser->is_active ? 'Đã kích hoạt' : 'Đã vô hiệu hóa';

        // Log admin action
        $this->logAdminAction(
            $admin,
            $action,
            'admin_user',
            $adminUser->id,
            $adminUser->username,
            ['is_active' => $oldStatus],
            ['is_active' => $adminUser->is_active],
            $message . ' admin user',
            request()->ip()
        );

        return redirect()->route('admin.admin-users.show', $adminUser->id)
            ->with('success', "{$message} admin user {$adminUser->username} thành công.");
    }

    private function logAdminAction($admin, $action, $targetType, $targetId, $targetName, $oldData, $newData, $reason, $ip)
    {
        DB::table('admin_action_logs')->insert([
            'admin_id' => $admin['id'],
            'admin_username' => $admin['username'],
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_name' => $targetName,
            'old_data' => json_encode($oldData),
            'new_data' => json_encode($newData),
            'reason' => $reason,
            'ip_address' => $ip,
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
