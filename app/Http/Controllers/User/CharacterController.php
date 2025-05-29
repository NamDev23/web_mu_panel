<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\UserAccount;
use App\Models\CharacterServiceLog;
use App\Models\UserTransactionLog;

class CharacterController extends Controller
{
    public function index()
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::with(['coinBalance', 'gameAccount'])->find($user['id']);

        // Get character service history
        $serviceHistory = CharacterServiceLog::where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get service costs
        $serviceCosts = CharacterServiceLog::getServiceCosts();

        return view('user.character.index', compact(
            'userAccount',
            'serviceHistory',
            'serviceCosts'
        ));
    }

    public function rename(Request $request)
    {
        $request->validate([
            'character_id' => 'required|integer',
            'new_name' => 'required|string|min:4|max:10|regex:/^[a-zA-Z0-9]+$/',
        ]);

        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        if (!$userAccount->game_account_id) {
            return back()->withErrors(['error' => 'Vui lòng liên kết tài khoản game trước.']);
        }

        $cost = CharacterServiceLog::getServiceCost('rename');

        // Check if user has enough coins
        if (!$userAccount->hasEnoughCoins($cost)) {
            return back()->withErrors(['error' => "Không đủ coin. Cần {$cost} coin để đổi tên nhân vật."]);
        }

        // Mock character data - in real implementation, get from game database
        $character = $this->getMockCharacter($request->character_id);
        if (!$character) {
            return back()->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        // Check if new name already exists (mock check)
        if ($this->isCharacterNameExists($request->new_name)) {
            return back()->withErrors(['error' => 'Tên nhân vật đã tồn tại.']);
        }

        try {
            DB::beginTransaction();

            // Create service log first
            $serviceLog = CharacterServiceLog::createRenameRequest(
                $user['id'],
                $request->character_id,
                $character['name'],
                $request->new_name,
                $cost
            );

            // Deduct coins with logging - reference to service log
            $userAccount->deductCoins(
                $cost,
                "Đổi tên nhân vật từ '{$character['name']}' thành '{$request->new_name}'"
            );

            // Log service purchase transaction
            UserTransactionLog::logServicePurchase(
                $user['id'],
                'character_rename',
                $cost,
                [
                    'character_id' => $request->character_id,
                    'old_name' => $character['name'],
                    'new_name' => $request->new_name,
                    'service_log_id' => $serviceLog->id
                ]
            );

            // In real implementation, this would call game database API
            // For now, just mark as completed
            $serviceLog->complete([
                'name' => $request->new_name
            ], 'Đổi tên thành công');

            DB::commit();

            return back()->with(
                'success',
                "Đã đổi tên nhân vật '{$character['name']}' thành '{$request->new_name}' thành công! " .
                    "Trừ " . number_format($cost) . " coin. Số dư hiện tại: " . number_format($userAccount->getCurrentCoins() - $cost) . " coin."
            );
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function resetStats(Request $request)
    {
        $request->validate([
            'character_id' => 'required|integer',
        ]);

        $user = Session::get('user_account');
        $userAccount = UserAccount::with('coinBalance')->find($user['id']);

        if (!$userAccount->game_account_id) {
            return back()->withErrors(['error' => 'Vui lòng liên kết tài khoản game trước.']);
        }

        $cost = CharacterServiceLog::getServiceCost('reset_stats');

        // Check if user has enough coins
        if (!$userAccount->hasEnoughCoins($cost)) {
            return back()->withErrors(['error' => "Không đủ coin. Cần {$cost} coin để reset điểm kỹ năng."]);
        }

        // Mock character data
        $character = $this->getMockCharacter($request->character_id);
        if (!$character) {
            return back()->withErrors(['error' => 'Không tìm thấy nhân vật.']);
        }

        try {
            DB::beginTransaction();

            // Create service log first
            $serviceLog = CharacterServiceLog::createResetStatsRequest(
                $user['id'],
                $request->character_id,
                $character['name'],
                $cost
            );

            // Deduct coins with logging - reference to service log
            $userAccount->deductCoins(
                $cost,
                "Reset điểm kỹ năng cho nhân vật '{$character['name']}'",
                $serviceLog
            );

            // Log service purchase transaction
            UserTransactionLog::logServicePurchase(
                $user['id'],
                'character_reset_stats',
                $cost,
                [
                    'character_id' => $request->character_id,
                    'character_name' => $character['name'],
                    'service_log_id' => $serviceLog->id
                ]
            );

            // Mock before data
            $beforeData = [
                'str' => 150,
                'agi' => 120,
                'vit' => 100,
                'ene' => 80
            ];

            // In real implementation, this would call game database API
            $serviceLog->complete([
                'str' => 0,
                'agi' => 0,
                'vit' => 0,
                'ene' => 0,
                'available_points' => 350
            ], 'Reset điểm kỹ năng thành công');

            DB::commit();

            return back()->with(
                'success',
                "Đã reset điểm kỹ năng cho nhân vật '{$character['name']}' thành công! " .
                    "Trừ {$cost} coin."
            );
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function getCharacters(Request $request)
    {
        $user = Session::get('user_account');
        $userAccount = UserAccount::find($user['id']);

        if (!$userAccount->game_account_id) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa liên kết tài khoản game'
            ]);
        }

        // Mock character data - in real implementation, get from game database
        $characters = $this->getMockCharacters($userAccount->game_account_id);

        return response()->json([
            'success' => true,
            'characters' => $characters
        ]);
    }

    public function serviceHistory(Request $request)
    {
        $user = Session::get('user_account');

        $query = CharacterServiceLog::where('user_id', $user['id']);

        // Filter by service type
        if ($request->has('service_type') && !empty($request->service_type)) {
            $query->where('service_type', $request->service_type);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('user.character.history', compact('services'));
    }

    // Mock methods - replace with real game database calls
    private function getMockCharacters($gameAccountId)
    {
        return [
            [
                'id' => 1,
                'name' => 'WarriorKing',
                'class' => 'Dark Knight',
                'level' => 350,
                'str' => 150,
                'agi' => 120,
                'vit' => 100,
                'ene' => 80
            ],
            [
                'id' => 2,
                'name' => 'MageQueen',
                'class' => 'Soul Master',
                'level' => 280,
                'str' => 50,
                'agi' => 80,
                'vit' => 70,
                'ene' => 200
            ],
            [
                'id' => 3,
                'name' => 'ElfArcher',
                'class' => 'Muse Elf',
                'level' => 320,
                'str' => 80,
                'agi' => 180,
                'vit' => 90,
                'ene' => 120
            ]
        ];
    }

    private function getMockCharacter($characterId)
    {
        $characters = $this->getMockCharacters(null);
        foreach ($characters as $character) {
            if ($character['id'] == $characterId) {
                return $character;
            }
        }
        return null;
    }

    private function isCharacterNameExists($name)
    {
        // Mock check - in real implementation, check game database
        $existingNames = ['Admin', 'GM', 'Test', 'System', 'WarriorKing', 'MageQueen'];
        return in_array($name, $existingNames);
    }
}
