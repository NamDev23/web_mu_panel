<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Character;
use App\Account;
use DB;

class CharacterController extends Controller
{
    /**
     * Search characters
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query');

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập tên nhân vật'
                ]);
            }

            $characters = Character::where('rname', 'like', "%{$query}%")
                                 ->with('account:UserID,UserName,Email,groupid')
                                 ->limit(20)
                                 ->get()
                                 ->map(function($char) {
                                     return [
                                         'rid' => $char->rid,
                                         'rname' => $char->rname,
                                         'level' => $char->level ?? 1,
                                         'server_name' => 'Server ' . ($char->serverid ?? 1),
                                         'account_name' => $char->account->UserName ?? 'Unknown',
                                         'account_email' => $char->account->Email ?? '',
                                         'is_banned' => ($char->account->groupid ?? 0) == 99,
                                         'userid' => $char->userid
                                     ];
                                 });

            return response()->json([
                'success' => true,
                'characters' => $characters
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get character details
     */
    public function show($id)
    {
        try {
            $character = Character::with('account')->findOrFail($id);

            $characterData = [
                'rid' => $character->rid,
                'rname' => $character->rname,
                'level' => $character->level ?? 1,
                'serverid' => $character->serverid ?? 1,
                'server_name' => 'Server ' . ($character->serverid ?? 1),
                'userid' => $character->userid,
                'account' => [
                    'UserID' => $character->account->UserID,
                    'UserName' => $character->account->UserName,
                    'Email' => $character->account->Email,
                    'Money' => $character->account->Money,
                    'groupid' => $character->account->groupid
                ],
                'stats' => [
                    'exp' => $character->exp ?? 0,
                    'gold' => $character->gold ?? 0,
                    'pk_count' => $character->pk_count ?? 0,
                    'online_time' => $character->online_time ?? 0
                ],
                'last_login' => $character->last_login,
                'created_at' => $character->created_at
            ];

            return response()->json([
                'success' => true,
                'character' => $characterData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ban character
     */
    public function ban($id)
    {
        try {
            $character = Character::findOrFail($id);
            
            // Ban the account that owns this character
            $account = Account::find($character->userid);
            if ($account) {
                $account->groupid = 99; // Banned group
                $account->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã ban nhân vật thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unban character
     */
    public function unban($id)
    {
        try {
            $character = Character::findOrFail($id);
            
            // Unban the account that owns this character
            $account = Account::find($character->userid);
            if ($account) {
                $account->groupid = 0; // Normal user group
                $account->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã unban nhân vật thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete character
     */
    public function destroy($id)
    {
        try {
            $character = Character::findOrFail($id);
            $characterName = $character->rname;
            
            $character->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa nhân vật {$characterName} thành công"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update character stats
     */
    public function updateStats(Request $request, $id)
    {
        try {
            $request->validate([
                'level' => 'nullable|integer|min:1|max:999',
                'exp' => 'nullable|integer|min:0',
                'gold' => 'nullable|integer|min:0'
            ]);

            $character = Character::findOrFail($id);
            
            if ($request->has('level')) {
                $character->level = $request->level;
            }
            if ($request->has('exp')) {
                $character->exp = $request->exp;
            }
            if ($request->has('gold')) {
                $character->gold = $request->gold;
            }
            
            $character->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin nhân vật thành công',
                'character' => $character
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get character inventory
     */
    public function inventory($id)
    {
        try {
            $character = Character::findOrFail($id);
            
            // Mock inventory data - replace with actual inventory table query
            $inventory = [
                [
                    'slot' => 1,
                    'item_id' => 1001,
                    'item_name' => 'Sword of Power',
                    'quantity' => 1,
                    'enhancement' => 5
                ],
                [
                    'slot' => 2,
                    'item_id' => 2001,
                    'item_name' => 'Health Potion',
                    'quantity' => 99,
                    'enhancement' => 0
                ]
            ];

            return response()->json([
                'success' => true,
                'inventory' => $inventory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
