<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GameDataService
{
    const CACHE_TTL = 300; // 5 minutes cache

    /**
     * Get game data for multiple accounts with caching
     */
    public function getAccountsGameData(array $accountIds)
    {
        $gameData = [];
        $uncachedIds = [];

        // Check cache first
        foreach ($accountIds as $accountId) {
            $cacheKey = "game_data_{$accountId}";
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                $gameData[$accountId] = $cached;
            } else {
                $uncachedIds[] = $accountId;
            }
        }

        // Fetch uncached data
        if (!empty($uncachedIds)) {
            $freshData = $this->fetchGameDataBatch($uncachedIds);
            
            // Cache the fresh data
            foreach ($freshData as $accountId => $data) {
                $cacheKey = "game_data_{$accountId}";
                Cache::put($cacheKey, $data, self::CACHE_TTL);
                $gameData[$accountId] = $data;
            }
        }

        return $gameData;
    }

    /**
     * Get game data for single account with caching
     */
    public function getAccountGameData($accountId)
    {
        $cacheKey = "game_data_{$accountId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($accountId) {
            return $this->fetchSingleAccountGameData($accountId);
        });
    }

    /**
     * Fetch game data for multiple accounts in batch
     */
    private function fetchGameDataBatch(array $accountIds)
    {
        $gameData = [];
        
        // Initialize default data
        foreach ($accountIds as $accountId) {
            $gameData[$accountId] = [
                'characters_count' => 0,
                'total_money' => 0
            ];
        }

        try {
            // Prepare game user IDs
            $gameUserIds = [];
            $idMapping = [];
            
            foreach ($accountIds as $accountId) {
                $gameUserId = 'ZT' . str_pad($accountId, 4, '0', STR_PAD_LEFT);
                $gameUserIds[] = $gameUserId;
                $idMapping[$gameUserId] = $accountId;
            }

            // Batch query for characters count
            $charactersData = DB::connection('game_mysql')
                ->table('t_roles')
                ->select('userid', DB::raw('COUNT(*) as count'))
                ->whereIn('userid', $gameUserIds)
                ->where('isdel', 0)
                ->groupBy('userid')
                ->get();

            foreach ($charactersData as $data) {
                if (isset($idMapping[$data->userid])) {
                    $accountId = $idMapping[$data->userid];
                    $gameData[$accountId]['characters_count'] = $data->count;
                }
            }

            // Batch query for money data
            $moneyData = DB::connection('game_mysql')
                ->table('t_money')
                ->select('userid', 'YuanBao')
                ->whereIn('userid', $gameUserIds)
                ->get();

            foreach ($moneyData as $data) {
                if (isset($idMapping[$data->userid])) {
                    $accountId = $idMapping[$data->userid];
                    $gameData[$accountId]['total_money'] = $data->YuanBao ?? 0;
                }
            }

        } catch (\Exception $e) {
            Log::error('Error fetching batch game data: ' . $e->getMessage());
        }

        return $gameData;
    }

    /**
     * Fetch game data for single account
     */
    private function fetchSingleAccountGameData($accountId)
    {
        $gameUserId = 'ZT' . str_pad($accountId, 4, '0', STR_PAD_LEFT);
        
        $data = [
            'characters_count' => 0,
            'total_money' => 0
        ];

        try {
            // Get characters count
            $data['characters_count'] = DB::connection('game_mysql')
                ->table('t_roles')
                ->where('userid', $gameUserId)
                ->where('isdel', 0)
                ->count();

            // Get money
            $money = DB::connection('game_mysql')
                ->table('t_money')
                ->where('userid', $gameUserId)
                ->value('YuanBao');
            
            $data['total_money'] = $money ?? 0;

        } catch (\Exception $e) {
            Log::error('Error fetching single account game data: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * Clear cache for specific account
     */
    public function clearAccountCache($accountId)
    {
        $cacheKey = "game_data_{$accountId}";
        Cache::forget($cacheKey);
    }

    /**
     * Clear all game data cache
     */
    public function clearAllCache()
    {
        // This is a simple implementation - in production you might want to use cache tags
        Cache::flush();
    }

    /**
     * Get characters list for account
     */
    public function getAccountCharacters($accountId, $limit = 10)
    {
        $cacheKey = "characters_{$accountId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($accountId, $limit) {
            $gameUserId = 'ZT' . str_pad($accountId, 4, '0', STR_PAD_LEFT);
            
            try {
                return DB::connection('game_mysql')
                    ->table('t_roles')
                    ->where('userid', $gameUserId)
                    ->where('isdel', 0)
                    ->orderBy('createtime', 'desc')
                    ->limit($limit)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error fetching characters: ' . $e->getMessage());
                return collect();
            }
        });
    }

    /**
     * Get account money details
     */
    public function getAccountMoneyDetails($accountId)
    {
        $cacheKey = "money_details_{$accountId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($accountId) {
            $gameUserId = 'ZT' . str_pad($accountId, 4, '0', STR_PAD_LEFT);
            
            try {
                return DB::connection('game_mysql')
                    ->table('t_money')
                    ->where('userid', $gameUserId)
                    ->first();
            } catch (\Exception $e) {
                Log::error('Error fetching money details: ' . $e->getMessage());
                return null;
            }
        });
    }
}
