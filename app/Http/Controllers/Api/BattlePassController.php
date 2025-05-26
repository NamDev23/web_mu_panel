<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BattlePassSeason;
use App\Models\BattlePassReward;
use App\Models\UserBattlePass;
use App\Models\BattlePassClaim;
use App\Account;
use DB;

class BattlePassController extends Controller
{
    /**
     * Display a listing of battle pass seasons
     */
    public function index(Request $request)
    {
        try {
            $query = BattlePassSeason::query();

            // Search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            // Status filter
            if ($request->has('status') && $request->status !== '') {
                switch ($request->status) {
                    case 'active':
                        $query->where('status', true)
                              ->where('start_date', '<=', now())
                              ->where('end_date', '>=', now());
                        break;
                    case 'upcoming':
                        $query->where('status', true)
                              ->where('start_date', '>', now());
                        break;
                    case 'ended':
                        $query->where('end_date', '<', now());
                        break;
                    case 'disabled':
                        $query->where('status', false);
                        break;
                }
            }

            $seasons = $query->withCount(['userProgress', 'rewards'])
                           ->orderBy('created_at', 'desc')
                           ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'seasons' => $seasons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created season
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'max_level' => 'required|integer|min:1|max:200',
                'price' => 'required|integer|min:0'
            ]);

            $season = BattlePassSeason::create($request->all());

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'create_battle_pass_season',
                'target_id' => $season->id,
                'details' => json_encode([
                    'name' => $request->name,
                    'max_level' => $request->max_level,
                    'price' => $request->price
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo season Battle Pass thành công',
                'season' => $season
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified season
     */
    public function show($id)
    {
        try {
            $season = BattlePassSeason::with(['rewards' => function($query) {
                $query->orderBy('level')->orderBy('is_premium');
            }])->findOrFail($id);

            $userCount = UserBattlePass::where('season_id', $id)->count();
            $premiumCount = UserBattlePass::where('season_id', $id)->where('is_premium', true)->count();

            return response()->json([
                'success' => true,
                'season' => $season,
                'stats' => [
                    'total_users' => $userCount,
                    'premium_users' => $premiumCount,
                    'free_users' => $userCount - $premiumCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified season
     */
    public function update(Request $request, $id)
    {
        try {
            $season = BattlePassSeason::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'max_level' => 'required|integer|min:1|max:200',
                'price' => 'required|integer|min:0',
                'status' => 'boolean'
            ]);

            $season->update($request->all());

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'update_battle_pass_season',
                'target_id' => $season->id,
                'details' => json_encode([
                    'name' => $request->name,
                    'status' => $request->status
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật season Battle Pass thành công',
                'season' => $season
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified season
     */
    public function destroy($id)
    {
        try {
            $season = BattlePassSeason::findOrFail($id);
            
            // Check if season has users
            $userCount = UserBattlePass::where('season_id', $id)->count();
            if ($userCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa season đã có người tham gia'
                ], 400);
            }

            // Log admin action before deletion
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'delete_battle_pass_season',
                'target_id' => $season->id,
                'details' => json_encode([
                    'name' => $season->name
                ]),
                'created_at' => now()
            ]);

            $season->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa season Battle Pass thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add reward to season
     */
    public function addReward(Request $request, $seasonId)
    {
        try {
            $request->validate([
                'level' => 'required|integer|min:1',
                'reward_type' => 'required|in:coin,item,exp',
                'reward_id' => 'required|integer',
                'reward_amount' => 'required|integer|min:1',
                'is_premium' => 'boolean'
            ]);

            $season = BattlePassSeason::findOrFail($seasonId);

            // Check if level is within season max level
            if ($request->level > $season->max_level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level reward không được vượt quá max level của season'
                ], 400);
            }

            // Check if reward already exists for this level and tier
            $existingReward = BattlePassReward::where('season_id', $seasonId)
                                           ->where('level', $request->level)
                                           ->where('is_premium', $request->has('is_premium'))
                                           ->first();

            if ($existingReward) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reward đã tồn tại cho level và tier này'
                ], 400);
            }

            $reward = BattlePassReward::create([
                'season_id' => $seasonId,
                'level' => $request->level,
                'reward_type' => $request->reward_type,
                'reward_id' => $request->reward_id,
                'reward_amount' => $request->reward_amount,
                'is_premium' => $request->has('is_premium')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm reward thành công',
                'reward' => $reward
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete reward
     */
    public function deleteReward($rewardId)
    {
        try {
            $reward = BattlePassReward::findOrFail($rewardId);
            $reward->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa reward thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user progress for a season
     */
    public function userProgress(Request $request)
    {
        try {
            $query = UserBattlePass::with(['user', 'season']);
            
            if ($request->has('season_id') && $request->season_id) {
                $query->where('season_id', $request->season_id);
            }
            
            if ($request->has('username') && $request->username) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('UserName', 'like', '%' . $request->username . '%');
                });
            }
            
            $userProgress = $query->orderBy('current_level', 'desc')
                                ->paginate($request->get('per_page', 20));
            
            $seasons = BattlePassSeason::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'user_progress' => $userProgress,
                'seasons' => $seasons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Purchase premium for user
     */
    public function purchasePremium(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|string',
                'season_id' => 'required|integer'
            ]);

            $userBattlePass = UserBattlePass::where([
                'user_id' => $request->user_id,
                'season_id' => $request->season_id
            ])->first();

            if (!$userBattlePass) {
                return response()->json([
                    'success' => false,
                    'message' => 'User chưa tham gia Battle Pass này'
                ], 404);
            }

            if ($userBattlePass->is_premium) {
                return response()->json([
                    'success' => false,
                    'message' => 'User đã có premium'
                ], 400);
            }

            $userBattlePass->purchasePremium();

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'purchase_battle_pass_premium',
                'target_id' => $userBattlePass->id,
                'details' => json_encode([
                    'user_id' => $request->user_id,
                    'season_id' => $request->season_id
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mua premium thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add experience to user
     */
    public function addExp(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|string',
                'season_id' => 'required|integer',
                'exp' => 'required|integer|min:1'
            ]);

            $userBattlePass = UserBattlePass::where([
                'user_id' => $request->user_id,
                'season_id' => $request->season_id
            ])->first();

            if (!$userBattlePass) {
                return response()->json([
                    'success' => false,
                    'message' => 'User chưa tham gia Battle Pass này'
                ], 404);
            }

            $oldLevel = $userBattlePass->current_level;
            $userBattlePass->addExp($request->exp);
            $newLevel = $userBattlePass->current_level;

            // Log admin action
            DB::table('admin_logs')->insert([
                'admin_id' => auth()->id(),
                'action' => 'add_battle_pass_exp',
                'target_id' => $userBattlePass->id,
                'details' => json_encode([
                    'user_id' => $request->user_id,
                    'season_id' => $request->season_id,
                    'exp_added' => $request->exp,
                    'old_level' => $oldLevel,
                    'new_level' => $newLevel
                ]),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm EXP thành công',
                'level_up' => $newLevel > $oldLevel,
                'new_level' => $newLevel
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
