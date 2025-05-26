<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recharge;
use App\Account;
use DB;

class AdminRechargeController extends Controller
{
    /**
     * Display a listing of recharge transactions
     */
    public function index(Request $request)
    {
        try {
            $query = Recharge::with('user');

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('serial', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('UserName', 'like', "%{$search}%");
                      });
                });
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Type filter
            if ($request->has('type') && $request->type !== '') {
                $query->where('type', $request->type);
            }

            // Date range filter
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $recharges = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'recharges' => $recharges
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created recharge (manual recharge)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|string',
                'amount' => 'required|integer|min:1000',
                'note' => 'nullable|string|max:255'
            ]);

            // Check if user exists
            $user = Account::where('UserID', $request->user_id)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy user'
                ], 404);
            }

            // Create recharge record
            $recharge = Recharge::create([
                'uid' => $request->user_id,
                'amount' => $request->amount,
                'type' => 5, // Manual recharge
                'status' => 1, // Success
                'serial' => 'MANUAL_' . time(),
                'code' => 'ADMIN_RECHARGE',
                'zoneid' => $user->Role,
                'note' => $request->note ?? 'Admin manual recharge'
            ]);

            // Calculate coin amount (example: 1 VND = 1 coin)
            $coinAmount = $request->amount;
            
            // Add coins to user account
            $oldBalance = $user->Money;
            $user->Money += $coinAmount;
            $user->save();

            // Log the transaction
            DB::table('coin_transactions')->insert([
                'user_id' => $request->user_id,
                'amount' => $coinAmount,
                'type' => 'recharge',
                'recharge_id' => $recharge->id,
                'admin_id' => auth()->id(),
                'old_balance' => $oldBalance,
                'new_balance' => $user->Money,
                'created_at' => now()
            ]);

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'manual_recharge',
                'target_id' => $recharge->id,
                'details' => json_encode([
                    'user_id' => $request->user_id,
                    'username' => $user->UserName,
                    'amount' => $request->amount,
                    'coin_amount' => $coinAmount
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nạp tiền thành công',
                'recharge' => $recharge,
                'new_balance' => $user->Money
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified recharge
     */
    public function show($id)
    {
        try {
            $recharge = Recharge::with('user')->findOrFail($id);

            // Get related coin transaction
            $coinTransaction = DB::table('coin_transactions')
                               ->where('recharge_id', $id)
                               ->first();

            return response()->json([
                'success' => true,
                'recharge' => $recharge,
                'coin_transaction' => $coinTransaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified recharge
     */
    public function update(Request $request, $id)
    {
        try {
            $recharge = Recharge::findOrFail($id);

            $request->validate([
                'status' => 'required|integer|in:0,1,2,3',
                'note' => 'nullable|string|max:255'
            ]);

            $oldStatus = $recharge->status;
            $recharge->update([
                'status' => $request->status,
                'note' => $request->note ?? $recharge->note
            ]);

            // If status changed from pending to success, add coins
            if ($oldStatus == 0 && $request->status == 1) {
                $user = Account::where('UserID', $recharge->uid)->first();
                if ($user) {
                    $coinAmount = $recharge->amount; // 1:1 ratio
                    $oldBalance = $user->Money;
                    $user->Money += $coinAmount;
                    $user->save();

                    // Log the transaction
                    DB::table('coin_transactions')->insert([
                        'user_id' => $recharge->uid,
                        'amount' => $coinAmount,
                        'type' => 'recharge',
                        'recharge_id' => $recharge->id,
                        'admin_id' => auth()->id(),
                        'old_balance' => $oldBalance,
                        'new_balance' => $user->Money,
                        'created_at' => now()
                    ]);
                }
            }

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'update_recharge',
                'target_id' => $recharge->id,
                'details' => json_encode([
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'note' => $request->note
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giao dịch thành công',
                'recharge' => $recharge
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve pending recharge
     */
    public function approve($id)
    {
        try {
            $recharge = Recharge::findOrFail($id);

            if ($recharge->status != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể duyệt giao dịch đang chờ'
                ], 400);
            }

            $recharge->status = 1;
            $recharge->save();

            // Add coins to user account
            $user = Account::where('UserID', $recharge->uid)->first();
            if ($user) {
                $coinAmount = $recharge->amount;
                $oldBalance = $user->Money;
                $user->Money += $coinAmount;
                $user->save();

                // Log the transaction
                DB::table('coin_transactions')->insert([
                    'user_id' => $recharge->uid,
                    'amount' => $coinAmount,
                    'type' => 'recharge',
                    'recharge_id' => $recharge->id,
                    'admin_id' => auth()->id(),
                    'old_balance' => $oldBalance,
                    'new_balance' => $user->Money,
                    'created_at' => now()
                ]);
            }

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'approve_recharge',
                'target_id' => $recharge->id,
                'details' => json_encode([
                    'user_id' => $recharge->uid,
                    'amount' => $recharge->amount
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Duyệt giao dịch thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject pending recharge
     */
    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:255'
            ]);

            $recharge = Recharge::findOrFail($id);

            if ($recharge->status != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể từ chối giao dịch đang chờ'
                ], 400);
            }

            $recharge->status = 2;
            $recharge->note = $request->reason ?? 'Rejected by admin';
            $recharge->save();

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'reject_recharge',
                'target_id' => $recharge->id,
                'details' => json_encode([
                    'user_id' => $recharge->uid,
                    'amount' => $recharge->amount,
                    'reason' => $request->reason
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Từ chối giao dịch thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recharge statistics
     */
    public function statistics(Request $request)
    {
        try {
            $dateFrom = $request->get('date_from', now()->startOfMonth());
            $dateTo = $request->get('date_to', now()->endOfMonth());

            // Total statistics
            $totalAmount = Recharge::where('status', 1)
                                 ->whereBetween('created_at', [$dateFrom, $dateTo])
                                 ->sum('amount');

            $totalTransactions = Recharge::whereBetween('created_at', [$dateFrom, $dateTo])->count();
            $successfulTransactions = Recharge::where('status', 1)
                                            ->whereBetween('created_at', [$dateFrom, $dateTo])
                                            ->count();
            $pendingTransactions = Recharge::where('status', 0)
                                         ->whereBetween('created_at', [$dateFrom, $dateTo])
                                         ->count();

            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_amount' => $totalAmount,
                    'total_transactions' => $totalTransactions,
                    'successful_transactions' => $successfulTransactions,
                    'pending_transactions' => $pendingTransactions,
                    'success_rate' => $totalTransactions > 0 ? round(($successfulTransactions / $totalTransactions) * 100, 2) : 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
