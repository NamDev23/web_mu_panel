<?php

namespace App\Console\Commands;

use App\Services\GameDataService;
use Illuminate\Console\Command;

class ClearGameDataCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-game-data {account_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear game data cache for specific account or all accounts';

    protected $gameDataService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GameDataService $gameDataService)
    {
        parent::__construct();
        $this->gameDataService = $gameDataService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $accountId = $this->argument('account_id');

        if ($accountId) {
            $this->gameDataService->clearAccountCache($accountId);
            $this->info("Cleared game data cache for account ID: {$accountId}");
        } else {
            $this->gameDataService->clearAllCache();
            $this->info("Cleared all game data cache");
        }

        return 0;
    }
}
