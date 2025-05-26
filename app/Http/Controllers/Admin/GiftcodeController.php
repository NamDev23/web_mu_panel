<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GiftcodeController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $search = $request->get('search');
        $statusFilter = $request->get('status', 'all');
        $typeFilter = $request->get('type', 'all');

        // Base query for giftcodes
        $query = DB::table('giftcodes as g')
            ->leftJoin('admin_users as a', 'g.created_by', '=', 'a.id')
            ->select([
                'g.*',
                'a.username as admin_username',
                DB::raw('(SELECT COUNT(*) FROM giftcode_usage WHERE giftcode_id = g.id) as usage_count')
            ]);

        // Apply search filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('g.code', 'like', "%{$search}%")
                    ->orWhere('g.name', 'like', "%{$search}%")
                    ->orWhere('g.description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($statusFilter !== 'all') {
            switch ($statusFilter) {
                case 'active':
                    $query->where('g.is_active', true)
                        ->where(function ($q) {
                            $q->whereNull('g.expires_at')
                                ->orWhere('g.expires_at', '>', now());
                        });
                    break;
                case 'expired':
                    $query->where('g.expires_at', '<=', now());
                    break;
                case 'inactive':
                    $query->where('g.is_active', false);
                    break;
                case 'used_up':
                    $query->whereRaw('g.used_count >= g.max_uses');
                    break;
            }
        }

        $giftcodes = $query->orderBy('g.created_at', 'desc')->paginate(20);

        // Get statistics
        $stats = [
            'total_giftcodes' => DB::table('giftcodes')->count(),
            'active_giftcodes' => DB::table('giftcodes')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->count(),
            'expired_giftcodes' => DB::table('giftcodes')
                ->where('expires_at', '<=', now())
                ->count(),
            'total_usage' => DB::table('giftcode_usage')->count()
        ];

        return view('admin.giftcodes.index', compact('admin', 'giftcodes', 'search', 'statusFilter', 'typeFilter', 'stats'));
    }

    public function create()
    {
        $admin = Session::get('admin_user');
        return view('admin.giftcodes.create', compact('admin'));
    }

    public function store(Request $request)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'code_type' => 'required|in:single,multiple',
            'code' => 'required_if:code_type,single|string|max:50|unique:giftcodes,code',
            'code_prefix' => 'required_if:code_type,multiple|string|max:20|regex:/^[A-Z0-9_]+$/',
            'code_count' => 'required_if:code_type,multiple|integer|min:1|max:1000',
            'max_uses' => 'required|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:now',
            'reward_type' => 'required|in:coins,items,mixed',
            'reward_coins' => 'required_if:reward_type,coins,mixed|integer|min:0',
            'reward_items' => 'required_if:reward_type,items,mixed|string',
        ], [
            'code_prefix.required_if' => 'Tiền tố code là bắt buộc khi tạo nhiều code.',
            'code_prefix.string' => 'Tiền tố code phải là chuỗi ký tự.',
            'code_prefix.regex' => 'Tiền tố code chỉ được chứa chữ cái, số và dấu gạch dưới.',
            'code.required_if' => 'Mã giftcode là bắt buộc khi tạo code đơn lẻ.',
            'code.unique' => 'Mã giftcode này đã tồn tại.',
            'code_count.required_if' => 'Số lượng code là bắt buộc khi tạo nhiều code.',
            'reward_coins.required_if' => 'Số coin thưởng là bắt buộc.',
            'reward_items.required_if' => 'Danh sách item thưởng là bắt buộc.',
        ]);

        // Generate codes
        $codes = [];
        if ($request->code_type === 'single') {
            $codes[] = strtoupper($request->code);
        } else {
            $prefix = strtoupper($request->code_prefix);
            for ($i = 1; $i <= $request->code_count; $i++) {
                $codes[] = $prefix . str_pad($i, 4, '0', STR_PAD_LEFT);
            }
        }

        // Prepare rewards
        $rewards = [];
        if ($request->reward_type === 'coins' || $request->reward_type === 'mixed') {
            $rewards['coins'] = $request->reward_coins;
        }
        if ($request->reward_type === 'items' || $request->reward_type === 'mixed') {
            $items = [];
            $itemLines = explode("\n", trim($request->reward_items));
            foreach ($itemLines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $parts = explode(',', $line);
                    if (count($parts) >= 2) {
                        $items[] = [
                            'id' => trim($parts[0]),
                            'quantity' => intval(trim($parts[1])),
                            'name' => isset($parts[2]) ? trim($parts[2]) : 'Item'
                        ];
                    }
                }
            }
            $rewards['items'] = $items;
        }

        // Create giftcodes
        foreach ($codes as $code) {
            DB::table('giftcodes')->insert([
                'code' => $code,
                'name' => $request->name,
                'description' => $request->description,
                'rewards' => json_encode($rewards),
                'max_uses' => $request->max_uses,
                'used_count' => 0,
                'is_active' => true,
                'expires_at' => $request->expires_at,
                'created_by' => $admin['id'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Log admin action
        $this->logAdminAction(
            $admin,
            'create_giftcode',
            'giftcode',
            $codes[0],
            $request->name,
            [],
            [
                'codes_count' => count($codes),
                'max_uses' => $request->max_uses,
                'rewards' => $rewards
            ],
            "Tạo " . count($codes) . " giftcode: " . $request->name,
            $request->ip()
        );

        return redirect()->route('admin.giftcodes.index')
            ->with('success', "Đã tạo thành công " . count($codes) . " giftcode!");
    }

    public function show($id)
    {
        $admin = Session::get('admin_user');

        $giftcode = DB::table('giftcodes as g')
            ->leftJoin('admin_users as a', 'g.created_by', '=', 'a.id')
            ->select([
                'g.*',
                'a.username as admin_username'
            ])
            ->where('g.id', $id)
            ->first();

        if (!$giftcode) {
            return redirect()->route('admin.giftcodes.index')->withErrors(['error' => 'Không tìm thấy giftcode.']);
        }

        // Get usage history
        $usageHistory = DB::table('giftcode_usage')
            ->where('giftcode_id', $id)
            ->orderBy('used_at', 'desc')
            ->limit(50)
            ->get();

        // Decode rewards
        $giftcode->rewards = json_decode($giftcode->rewards, true);

        return view('admin.giftcodes.show', compact('admin', 'giftcode', 'usageHistory'));
    }

    public function edit($id)
    {
        $admin = Session::get('admin_user');

        $giftcode = DB::table('giftcodes')->where('id', $id)->first();

        if (!$giftcode) {
            return redirect()->route('admin.giftcodes.index')->withErrors(['error' => 'Không tìm thấy giftcode.']);
        }

        // Decode rewards
        $giftcode->rewards = json_decode($giftcode->rewards, true);

        return view('admin.giftcodes.edit', compact('admin', 'giftcode'));
    }

    public function update(Request $request, $id)
    {
        $admin = Session::get('admin_user');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'max_uses' => 'required|integer|min:1|max:10000',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        // Get giftcode info before update
        $giftcode = DB::table('giftcodes')->where('id', $id)->first();
        if (!$giftcode) {
            return redirect()->route('admin.giftcodes.index')->withErrors(['error' => 'Không tìm thấy giftcode.']);
        }

        $oldData = [
            'name' => $giftcode->name,
            'max_uses' => $giftcode->max_uses,
            'is_active' => $giftcode->is_active,
            'expires_at' => $giftcode->expires_at,
        ];

        $newData = [
            'name' => $request->name,
            'max_uses' => $request->max_uses,
            'is_active' => $request->has('is_active'),
            'expires_at' => $request->expires_at,
        ];

        // Update giftcode
        DB::table('giftcodes')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'max_uses' => $request->max_uses,
                'is_active' => $request->has('is_active'),
                'expires_at' => $request->expires_at,
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'edit_giftcode',
            'giftcode',
            $id,
            $giftcode->code,
            $oldData,
            $newData,
            'Cập nhật thông tin giftcode',
            $request->ip()
        );

        return redirect()->route('admin.giftcodes.show', $id)
            ->with('success', "Đã cập nhật giftcode {$giftcode->code} thành công.");
    }

    public function destroy($id)
    {
        $admin = Session::get('admin_user');

        // Get giftcode info before delete
        $giftcode = DB::table('giftcodes')->where('id', $id)->first();
        if (!$giftcode) {
            return redirect()->route('admin.giftcodes.index')->withErrors(['error' => 'Không tìm thấy giftcode.']);
        }

        // Delete giftcode
        DB::table('giftcodes')->where('id', $id)->delete();

        // Log admin action
        $this->logAdminAction(
            $admin,
            'delete_giftcode',
            'giftcode',
            $id,
            $giftcode->code,
            ['code' => $giftcode->code, 'name' => $giftcode->name],
            [],
            'Xóa giftcode',
            request()->ip()
        );

        return redirect()->route('admin.giftcodes.index')
            ->with('success', "Đã xóa giftcode {$giftcode->code} thành công.");
    }

    public function toggleStatus($id)
    {
        $admin = Session::get('admin_user');

        $giftcode = DB::table('giftcodes')->where('id', $id)->first();
        if (!$giftcode) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy giftcode']);
        }

        $newStatus = !$giftcode->is_active;

        DB::table('giftcodes')
            ->where('id', $id)
            ->update([
                'is_active' => $newStatus,
                'updated_at' => now()
            ]);

        // Log admin action
        $this->logAdminAction(
            $admin,
            'toggle_giftcode_status',
            'giftcode',
            $id,
            $giftcode->code,
            ['is_active' => $giftcode->is_active],
            ['is_active' => $newStatus],
            $newStatus ? 'Kích hoạt giftcode' : 'Vô hiệu hóa giftcode',
            request()->ip()
        );

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'Đã kích hoạt giftcode' : 'Đã vô hiệu hóa giftcode',
            'is_active' => $newStatus
        ]);
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
