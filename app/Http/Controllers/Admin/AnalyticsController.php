<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $admin = Session::get('admin_user');
        $period = $request->get('period', '7'); // 7, 30, 90 days

        // Calculate date range
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($period);

        // Get overview statistics
        $stats = $this->getOverviewStats($startDate, $endDate);

        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate);

        // Get top performers
        $topData = $this->getTopPerformers();

        return view('admin.analytics.index', compact('admin', 'stats', 'chartData', 'topData', 'period'));
    }

    private function getOverviewStats($startDate, $endDate)
    {
        // Account statistics - using t_account table
        $totalAccounts = DB::table('t_account')->count();
        $newAccounts = DB::table('t_account')
            ->whereBetween('CreateTime', [$startDate, $endDate])
            ->count();
        $activeAccounts = DB::table('t_account')
            ->where('LastLoginTime', '>=', $startDate)
            ->count();
        $bannedAccounts = DB::table('t_account')
            ->where('Status', 0) // 0 = banned
            ->count();

        // Character statistics - using game database
        $totalCharacters = DB::connection('game_mysql')->table('t_roles')->where('isdel', 0)->count();
        $newCharacters = DB::connection('game_mysql')->table('t_roles')
            ->where('isdel', 0)
            ->whereBetween('regtime', [$startDate, $endDate])
            ->count();
        $activeCharacters = DB::connection('game_mysql')->table('t_roles')
            ->where('isdel', 0)
            ->where('lasttime', '>=', $startDate)
            ->count();

        // Revenue statistics - Coin Recharge (using coin_recharge_logs)
        $coinRechargeTotal = DB::table('coin_recharge_logs')
            ->where('status', 'completed')
            ->sum('amount_vnd');
        $coinRechargePeriod = DB::table('coin_recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount_vnd');
        $coinRechargeTransactions = DB::table('coin_recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Revenue statistics - Monthly Cards & Battle Pass (disabled - table not exists)
        $monthlyCardsTotal = 0;
        $monthlyCardsPeriod = 0;
        $monthlyCardsTransactions = 0;

        // Combined revenue statistics
        $totalRevenue = $coinRechargeTotal + $monthlyCardsTotal;
        $periodRevenue = $coinRechargePeriod + $monthlyCardsPeriod;
        $totalTransactions = $coinRechargeTransactions + $monthlyCardsTransactions;
        $avgTransactionValue = $totalTransactions > 0 ? $periodRevenue / $totalTransactions : 0;

        // Giftcode statistics (disabled - table not exists)
        $totalGiftcodes = 0;
        $activeGiftcodes = 0;
        $giftcodeUsage = 0;

        // Calculate growth rates (compared to previous period)
        $prevStartDate = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $prevEndDate = $startDate->copy();

        $prevNewAccounts = DB::table('t_account')
            ->whereBetween('CreateTime', [$prevStartDate, $prevEndDate])
            ->count();
        $prevRevenue = DB::table('coin_recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('amount_vnd');

        $accountGrowth = $prevNewAccounts > 0 ? (($newAccounts - $prevNewAccounts) / $prevNewAccounts) * 100 : 0;
        $revenueGrowth = $prevRevenue > 0 ? (($periodRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        return [
            'accounts' => [
                'total' => $totalAccounts,
                'new' => $newAccounts,
                'active' => $activeAccounts,
                'banned' => $bannedAccounts,
                'growth' => $accountGrowth
            ],
            'characters' => [
                'total' => $totalCharacters,
                'new' => $newCharacters,
                'active' => $activeCharacters
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'period' => $periodRevenue,
                'transactions' => $totalTransactions,
                'avg_value' => $avgTransactionValue,
                'growth' => $revenueGrowth,
                'breakdown' => [
                    'coin_recharge' => [
                        'total' => $coinRechargeTotal,
                        'period' => $coinRechargePeriod,
                        'transactions' => $coinRechargeTransactions
                    ],
                    'monthly_cards' => [
                        'total' => $monthlyCardsTotal,
                        'period' => $monthlyCardsPeriod,
                        'transactions' => $monthlyCardsTransactions
                    ]
                ]
            ],
            'giftcodes' => [
                'total' => $totalGiftcodes,
                'active' => $activeGiftcodes,
                'usage' => $giftcodeUsage
            ],
            'monthly_cards' => [
                'total' => 0,
                'active' => 0,
                'revenue' => 0
            ],
            'battle_pass' => [
                'total' => 0,
                'active' => 0,
                'revenue' => 0
            ]
        ];
    }

    private function getChartData($startDate, $endDate)
    {
        // Daily registration chart (using t_account)
        $registrations = DB::table('t_account')
            ->select(DB::raw('DATE(CreateTime) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('CreateTime', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(CreateTime)'))
            ->orderBy('date')
            ->get();

        // Daily revenue chart - Coin Recharge (using coin_recharge_logs)
        $coinRevenues = DB::table('coin_recharge_logs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount_vnd) as amount'))
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Daily revenue chart - Monthly Cards & Battle Pass (disabled - table not exists)
        $cardRevenues = collect();

        // Combine revenue data
        $revenues = $coinRevenues;

        // Server distribution (disabled - no serverid column)
        $serverStats = collect([
            (object) ['serverid' => 'Server 1', 'count' => DB::connection('game_mysql')->table('t_roles')->where('isdel', 0)->count()]
        ]);

        // Level distribution (using game database)
        $levelStats = DB::connection('game_mysql')->table('t_roles')
            ->select(
                DB::raw('CASE
                    WHEN level BETWEEN 1 AND 10 THEN "1-10"
                    WHEN level BETWEEN 11 AND 50 THEN "11-50"
                    WHEN level BETWEEN 51 AND 100 THEN "51-100"
                    WHEN level BETWEEN 101 AND 200 THEN "101-200"
                    ELSE "200+"
                END as level_range'),
                DB::raw('COUNT(*) as count')
            )
            ->where('isdel', 0)
            ->groupBy('level_range')
            ->get();

        return [
            'registrations' => $registrations,
            'revenues' => $revenues,
            'card_revenues' => $cardRevenues,
            'servers' => $serverStats,
            'levels' => $levelStats
        ];
    }

    private function getTopPerformers()
    {
        // Top spenders (simplified - no join to avoid collation issues)
        $topSpenders = DB::table('coin_recharge_logs')
            ->select('username', DB::raw('SUM(amount_vnd) as total_spent'), DB::raw('COUNT(id) as transaction_count'))
            ->where('status', 'completed')
            ->groupBy('username')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Top characters by level (using game database)
        // Need to get username from t_account table by converting userid format
        $topCharacters = DB::connection('game_mysql')->table('t_roles')
            ->select('rname', 'level', 'userid')
            ->where('isdel', 0)
            ->orderBy('level', 'desc')
            ->limit(10)
            ->get();

        // Add username and serverid to each character by looking up in t_account table
        foreach ($topCharacters as $character) {
            // Convert userid format: ZT0012 -> 12
            $accountId = (int) str_replace('ZT', '', ltrim($character->userid, 'ZT0'));

            // Get username from t_account table
            $account = DB::table('t_account')
                ->select('UserName')
                ->where('ID', $accountId)
                ->first();

            $character->username = $account ? $account->UserName : 'Unknown';
            $character->serverid = 1; // Default server since serverid column doesn't exist
        }

        // Most used giftcodes (disabled - table not exists)
        $topGiftcodes = collect();

        // Recent admin actions (simplified - no join with admin_users)
        $recentActions = DB::table('admin_action_logs')
            ->select('*')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        // Top Monthly Cards & Battle Pass (simplified - no join)
        $topMonthlyCards = collect(); // Empty collection since monthly_cards table may not exist

        return [
            'spenders' => $topSpenders,
            'characters' => $topCharacters,
            'giftcodes' => $topGiftcodes,
            'monthly_cards' => $topMonthlyCards,
            'actions' => $recentActions
        ];
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'overview');
        $period = $request->get('period', '30');

        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($period);

        switch ($type) {
            case 'accounts':
                return $this->exportAccounts($startDate, $endDate);
            case 'revenue':
                return $this->exportRevenue($startDate, $endDate);
            case 'characters':
                return $this->exportCharacters($startDate, $endDate);
            case 'monthly_cards':
                return $this->exportMonthlyCards($startDate, $endDate);
            default:
                return $this->exportOverview($startDate, $endDate);
        }
    }

    private function exportOverview($startDate, $endDate)
    {
        $stats = $this->getOverviewStats($startDate, $endDate);

        $filename = 'analytics_overview_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($stats) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Headers
            fputcsv($file, ['Danh mục', 'Giá trị', 'Ghi chú'], ';');

            // Empty row for separation
            fputcsv($file, [''], ';');

            // Account stats section
            fputcsv($file, ['=== THỐNG KÊ TÀI KHOẢN ===', '', ''], ';');
            fputcsv($file, ['Tổng số tài khoản', $stats['accounts']['total'], 'accounts'], ';');
            fputcsv($file, ['Tài khoản mới', $stats['accounts']['new'], 'accounts'], ';');
            fputcsv($file, ['Tài khoản hoạt động', $stats['accounts']['active'], 'accounts'], ';');
            fputcsv($file, ['Tài khoản bị khóa', $stats['accounts']['banned'], 'accounts'], ';');
            fputcsv($file, ['Tăng trưởng tài khoản', number_format($stats['accounts']['growth'], 1) . '%', 'percentage'], ';');

            // Empty row
            fputcsv($file, [''], ';');

            // Revenue stats section
            fputcsv($file, ['=== THỐNG KÊ DOANH THU ===', '', ''], ';');
            fputcsv($file, ['Tổng doanh thu', $stats['revenue']['total'], 'VND'], ';');
            fputcsv($file, ['Doanh thu kỳ này', $stats['revenue']['period'], 'VND'], ';');
            fputcsv($file, ['Tổng giao dịch', $stats['revenue']['transactions'], 'transactions'], ';');
            fputcsv($file, ['Giá trị TB/giao dịch', $stats['revenue']['avg_value'], 'VND'], ';');
            fputcsv($file, ['Tăng trưởng doanh thu', number_format($stats['revenue']['growth'], 1) . '%', 'percentage'], ';');

            // Revenue breakdown
            fputcsv($file, [''], ';');
            fputcsv($file, ['--- Chi tiết doanh thu ---', '', ''], ';');
            fputcsv($file, ['Nạp Coin - Tổng', $stats['revenue']['breakdown']['coin_recharge']['total'], 'VND'], ';');
            fputcsv($file, ['Nạp Coin - Kỳ này', $stats['revenue']['breakdown']['coin_recharge']['period'], 'VND'], ';');
            fputcsv($file, ['Nạp Coin - Giao dịch', $stats['revenue']['breakdown']['coin_recharge']['transactions'], 'transactions'], ';');
            fputcsv($file, ['Monthly Cards - Tổng', $stats['revenue']['breakdown']['monthly_cards']['total'], 'VND'], ';');
            fputcsv($file, ['Monthly Cards - Kỳ này', $stats['revenue']['breakdown']['monthly_cards']['period'], 'VND'], ';');
            fputcsv($file, ['Monthly Cards - Giao dịch', $stats['revenue']['breakdown']['monthly_cards']['transactions'], 'transactions'], ';');

            // Empty row
            fputcsv($file, [''], ';');

            // Character stats section
            fputcsv($file, ['=== THỐNG KÊ NHÂN VẬT ===', '', ''], ';');
            fputcsv($file, ['Tổng số nhân vật', $stats['characters']['total'], 'characters'], ';');
            fputcsv($file, ['Nhân vật mới', $stats['characters']['new'], 'characters'], ';');
            fputcsv($file, ['Nhân vật hoạt động', $stats['characters']['active'], 'characters'], ';');

            // Empty row
            fputcsv($file, [''], ';');

            // Monthly Cards & Battle Pass stats
            fputcsv($file, ['=== MONTHLY CARDS & BATTLE PASS ===', '', ''], ';');
            fputcsv($file, ['Monthly Cards - Tổng', $stats['monthly_cards']['total'], 'cards'], ';');
            fputcsv($file, ['Monthly Cards - Hoạt động', $stats['monthly_cards']['active'], 'cards'], ';');
            fputcsv($file, ['Monthly Cards - Doanh thu', $stats['monthly_cards']['revenue'], 'VND'], ';');
            fputcsv($file, ['Battle Pass - Tổng', $stats['battle_pass']['total'], 'passes'], ';');
            fputcsv($file, ['Battle Pass - Hoạt động', $stats['battle_pass']['active'], 'passes'], ';');
            fputcsv($file, ['Battle Pass - Doanh thu', $stats['battle_pass']['revenue'], 'VND'], ';');

            // Empty row
            fputcsv($file, [''], ';');

            // Giftcode stats section
            fputcsv($file, ['=== THỐNG KÊ GIFTCODE ===', '', ''], ';');
            fputcsv($file, ['Tổng giftcode', $stats['giftcodes']['total'], 'codes'], ';');
            fputcsv($file, ['Giftcode hoạt động', $stats['giftcodes']['active'], 'codes'], ';');
            fputcsv($file, ['Lượt sử dụng kỳ này', $stats['giftcodes']['usage'], 'usages'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportAccounts($startDate, $endDate)
    {
        $accounts = DB::table('t_account')
            ->whereBetween('CreateTime', [$startDate, $endDate])
            ->orderBy('CreateTime', 'desc')
            ->get();

        $filename = 'accounts_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($accounts) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($file, ['ID', 'Username', 'Email', 'Trạng thái', 'Ngày tạo', 'Lần đăng nhập cuối'], ';');

            foreach ($accounts as $account) {
                $statusLabel = $account->Status == 1 ? 'Hoạt động' : 'Bị khóa';

                fputcsv($file, [
                    $account->ID,
                    $account->UserName,
                    $account->Email ?? '',
                    $statusLabel,
                    date('d/m/Y H:i:s', strtotime($account->CreateTime)),
                    $account->LastLoginTime ? date('d/m/Y H:i:s', strtotime($account->LastLoginTime)) : 'Chưa đăng nhập'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRevenue($startDate, $endDate)
    {
        // Get coin recharge transactions
        $coinTransactions = DB::table('coin_recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Get monthly cards & battle pass transactions (empty for now)
        $cardTransactions = collect(); // Empty collection since monthly_cards table may not exist

        $filename = 'revenue_complete_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($coinTransactions, $cardTransactions) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Coin Recharge Section
            fputcsv($file, ['=== NẠP COIN TRANSACTIONS ==='], ';');
            fputcsv($file, ['ID', 'Username', 'Số tiền (VND)', 'Coins nhận', 'Loại', 'Transaction ID', 'Ngày tạo'], ';');

            foreach ($coinTransactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->username,
                    number_format($transaction->amount_vnd),
                    number_format($transaction->coins_added),
                    $transaction->type,
                    $transaction->transaction_id,
                    date('d/m/Y H:i:s', strtotime($transaction->created_at))
                ], ';');
            }

            // Empty rows for separation
            fputcsv($file, [''], ';');
            fputcsv($file, [''], ';');

            // Monthly Cards & Battle Pass Section
            fputcsv($file, ['=== MONTHLY CARDS & BATTLE PASS ==='], ';');
            fputcsv($file, ['ID', 'Username', 'Email', 'Loại', 'Tên gói', 'Giá (VND)', 'Trạng thái', 'Ngày mua'], ';');

            foreach ($cardTransactions as $card) {
                $typeLabel = $card->type == 'monthly_card' ? 'Monthly Card' : 'Battle Pass';
                fputcsv($file, [
                    $card->id,
                    $card->username,
                    $card->email,
                    $typeLabel,
                    $card->package_name,
                    number_format($card->amount),
                    $card->status,
                    date('d/m/Y H:i:s', strtotime($card->created_at))
                ], ';');
            }

            // Summary section
            fputcsv($file, [''], ';');
            fputcsv($file, [''], ';');
            fputcsv($file, ['=== TỔNG KẾT ==='], ';');

            $coinTotal = $coinTransactions->sum('amount_vnd');
            $cardTotal = $cardTransactions->sum('amount_vnd');
            $grandTotal = $coinTotal + $cardTotal;

            fputcsv($file, ['Tổng nạp coin', number_format($coinTotal), 'VND'], ';');
            fputcsv($file, ['Tổng Monthly Cards & Battle Pass', number_format($cardTotal), 'VND'], ';');
            fputcsv($file, ['TỔNG DOANH THU', number_format($grandTotal), 'VND'], ';');
            fputcsv($file, ['Số giao dịch nạp coin', count($coinTransactions), 'transactions'], ';');
            fputcsv($file, ['Số giao dịch cards/pass', count($cardTransactions), 'transactions'], ';');
            fputcsv($file, ['Tổng giao dịch', count($coinTransactions) + count($cardTransactions), 'transactions'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCharacters($startDate, $endDate)
    {
        $characters = DB::connection('game_mysql')->table('t_roles')
            ->whereBetween('regtime', [$startDate, $endDate])
            ->where('isdel', 0)
            ->orderBy('level', 'desc')
            ->get();

        $filename = 'characters_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($characters) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($file, ['Character ID', 'Tên nhân vật', 'User ID', 'Level', 'Nghề nghiệp', 'Tiền', 'Ngày tạo', 'Lần cuối online'], ';');

            foreach ($characters as $character) {
                fputcsv($file, [
                    $character->rid,
                    $character->rname,
                    $character->userid,
                    $character->level,
                    $character->occupation,
                    number_format($character->money ?? 0),
                    date('d/m/Y H:i:s', strtotime($character->regtime)),
                    date('d/m/Y H:i:s', strtotime($character->lasttime))
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportMonthlyCards($startDate, $endDate)
    {
        // Monthly cards table may not exist, return empty data
        $monthlyCards = collect();

        $filename = 'monthly_cards_battle_pass_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($monthlyCards) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header
            fputcsv($file, [
                'ID',
                'Loại',
                'Username',
                'Email',
                'Họ tên',
                'VIP Level',
                'Tên gói',
                'Giá (VND)',
                'Thời hạn (ngày)',
                'Trạng thái',
                'Ngày mua',
                'Hết hạn',
                'Tạo bởi',
                'Mô tả'
            ], ';');

            foreach ($monthlyCards as $card) {
                $typeLabel = $card->type == 'monthly_card' ? 'Monthly Card' : 'Battle Pass';

                switch ($card->status) {
                    case 'active':
                        $statusLabel = 'Hoạt động';
                        break;
                    case 'expired':
                        $statusLabel = 'Hết hạn';
                        break;
                    case 'cancelled':
                        $statusLabel = 'Đã hủy';
                        break;
                    default:
                        $statusLabel = $card->status;
                        break;
                }

                fputcsv($file, [
                    $card->id,
                    $typeLabel,
                    $card->username,
                    $card->email ?? 'N/A',
                    $card->full_name ?? 'N/A',
                    $card->vip_level ?? 0,
                    $card->package_name,
                    number_format($card->price),
                    $card->duration_days,
                    $statusLabel,
                    date('d/m/Y H:i:s', strtotime($card->purchased_at)),
                    date('d/m/Y H:i:s', strtotime($card->expires_at)),
                    $card->created_by_username ?? 'N/A',
                    $card->description ?? ''
                ], ';');
            }

            // Summary section
            fputcsv($file, [''], ';');
            fputcsv($file, ['=== THỐNG KÊ ==='], ';');

            $monthlyCardsCount = $monthlyCards->where('type', 'monthly_card')->count();
            $battlePassCount = $monthlyCards->where('type', 'battle_pass')->count();
            $monthlyCardsRevenue = $monthlyCards->where('type', 'monthly_card')->where('status', '!=', 'cancelled')->sum('price');
            $battlePassRevenue = $monthlyCards->where('type', 'battle_pass')->where('status', '!=', 'cancelled')->sum('price');

            fputcsv($file, ['Tổng Monthly Cards', $monthlyCardsCount, 'cards'], ';');
            fputcsv($file, ['Tổng Battle Pass', $battlePassCount, 'passes'], ';');
            fputcsv($file, ['Doanh thu Monthly Cards', number_format($monthlyCardsRevenue), 'VND'], ';');
            fputcsv($file, ['Doanh thu Battle Pass', number_format($battlePassRevenue), 'VND'], ';');
            fputcsv($file, ['Tổng doanh thu', number_format($monthlyCardsRevenue + $battlePassRevenue), 'VND'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
