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
        // Account statistics
        $totalAccounts = DB::table('game_accounts')->count();
        $newAccounts = DB::table('game_accounts')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $activeAccounts = DB::table('game_accounts')
            ->where('updated_at', '>=', $startDate)
            ->count();
        $bannedAccounts = DB::table('game_accounts')
            ->where('status', 'banned')
            ->count();

        // Character statistics
        $totalCharacters = DB::table('t_roles')->count();
        $newCharacters = DB::table('t_roles')
            ->whereBetween('regtime', [$startDate, $endDate])
            ->count();
        $activeCharacters = DB::table('t_roles')
            ->where('lasttime', '>=', $startDate)
            ->count();

        // Revenue statistics - Coin Recharge
        $coinRechargeTotal = DB::table('recharge_logs')
            ->where('status', 'completed')
            ->sum('amount');
        $coinRechargePeriod = DB::table('recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        $coinRechargeTransactions = DB::table('recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Revenue statistics - Monthly Cards & Battle Pass
        $monthlyCardsTotal = DB::table('monthly_cards')
            ->where('status', '!=', 'cancelled')
            ->sum('price');
        $monthlyCardsPeriod = DB::table('monthly_cards')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('price');
        $monthlyCardsTransactions = DB::table('monthly_cards')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Combined revenue statistics
        $totalRevenue = $coinRechargeTotal + $monthlyCardsTotal;
        $periodRevenue = $coinRechargePeriod + $monthlyCardsPeriod;
        $totalTransactions = $coinRechargeTransactions + $monthlyCardsTransactions;
        $avgTransactionValue = $totalTransactions > 0 ? $periodRevenue / $totalTransactions : 0;

        // Giftcode statistics
        $totalGiftcodes = DB::table('giftcodes')->count();
        $activeGiftcodes = DB::table('giftcodes')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();
        $giftcodeUsage = DB::table('giftcode_usage')
            ->whereBetween('used_at', [$startDate, $endDate])
            ->count();

        // Calculate growth rates (compared to previous period)
        $prevStartDate = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $prevEndDate = $startDate->copy();

        $prevNewAccounts = DB::table('game_accounts')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->count();
        $prevRevenue = DB::table('recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('amount');

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
                'total' => DB::table('monthly_cards')->where('type', 'monthly_card')->count(),
                'active' => DB::table('monthly_cards')->where('type', 'monthly_card')->where('status', 'active')->count(),
                'revenue' => DB::table('monthly_cards')->where('type', 'monthly_card')->where('status', '!=', 'cancelled')->sum('price')
            ],
            'battle_pass' => [
                'total' => DB::table('monthly_cards')->where('type', 'battle_pass')->count(),
                'active' => DB::table('monthly_cards')->where('type', 'battle_pass')->where('status', 'active')->count(),
                'revenue' => DB::table('monthly_cards')->where('type', 'battle_pass')->where('status', '!=', 'cancelled')->sum('price')
            ]
        ];
    }

    private function getChartData($startDate, $endDate)
    {
        // Daily registration chart
        $registrations = DB::table('game_accounts')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Daily revenue chart - Coin Recharge
        $coinRevenues = DB::table('recharge_logs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as amount'))
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Daily revenue chart - Monthly Cards & Battle Pass
        $cardRevenues = DB::table('monthly_cards')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(price) as amount'), 'type')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'), 'type')
            ->orderBy('date')
            ->get();

        // Combine revenue data
        $revenues = $coinRevenues;

        // Server distribution
        $serverStats = DB::table('t_roles')
            ->select('serverid', DB::raw('COUNT(*) as count'))
            ->groupBy('serverid')
            ->orderBy('count', 'desc')
            ->get();

        // Level distribution
        $levelStats = DB::table('t_roles')
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
        // Top spenders
        $topSpenders = DB::table('recharge_logs as r')
            ->leftJoin('game_accounts as a', 'r.username', '=', 'a.username')
            ->select('r.username', 'a.email', DB::raw('SUM(r.amount) as total_spent'), DB::raw('COUNT(r.id) as transaction_count'))
            ->where('r.status', 'completed')
            ->groupBy('r.username', 'a.email')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Top characters by level
        $topCharacters = DB::table('t_roles as c')
            ->leftJoin('game_accounts as a', 'c.userid', '=', 'a.id')
            ->select('c.rname', 'c.level', 'c.serverid', 'a.username')
            ->where('c.isdel', 0)
            ->orderBy('c.level', 'desc')
            ->limit(10)
            ->get();

        // Most used giftcodes
        $topGiftcodes = DB::table('giftcodes')
            ->select('code', 'name', 'used_count', 'max_uses')
            ->where('used_count', '>', 0)
            ->orderBy('used_count', 'desc')
            ->limit(10)
            ->get();

        // Recent admin actions
        $recentActions = DB::table('admin_action_logs as l')
            ->leftJoin('admin_users as a', 'l.admin_id', '=', 'a.id')
            ->select('l.*', 'a.username as admin_username')
            ->orderBy('l.created_at', 'desc')
            ->limit(15)
            ->get();

        // Top Monthly Cards & Battle Pass
        $topMonthlyCards = DB::table('monthly_cards as m')
            ->leftJoin('game_accounts as a', 'm.username', '=', 'a.username')
            ->select('m.package_name', 'm.type', 'm.price', 'm.username', 'a.email', DB::raw('COUNT(*) as purchase_count'))
            ->where('m.status', '!=', 'cancelled')
            ->groupBy('m.package_name', 'm.type', 'm.price', 'm.username', 'a.email')
            ->orderBy('purchase_count', 'desc')
            ->limit(5)
            ->get();

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
        $admin = Session::get('admin_user');
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
        $accounts = DB::table('game_accounts')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
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
            fputcsv($file, ['ID', 'Username', 'Email', 'Họ tên', 'Trạng thái', 'VIP Level', 'Số dư hiện tại', 'Ngày tạo', 'Cập nhật cuối'], ';');

            foreach ($accounts as $account) {
                switch ($account->status) {
                    case 'active':
                        $statusLabel = 'Hoạt động';
                        break;
                    case 'banned':
                        $statusLabel = 'Bị khóa';
                        break;
                    case 'suspended':
                        $statusLabel = 'Tạm khóa';
                        break;
                    default:
                        $statusLabel = $account->status;
                        break;
                }

                fputcsv($file, [
                    $account->id,
                    $account->username,
                    $account->email,
                    $account->full_name ?? '',
                    $statusLabel,
                    $account->vip_level ?? 0,
                    number_format($account->current_balance ?? 0),
                    date('d/m/Y H:i:s', strtotime($account->created_at)),
                    date('d/m/Y H:i:s', strtotime($account->updated_at))
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRevenue($startDate, $endDate)
    {
        // Get coin recharge transactions
        $coinTransactions = DB::table('recharge_logs')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Get monthly cards & battle pass transactions
        $cardTransactions = DB::table('monthly_cards as m')
            ->leftJoin('game_accounts as a', 'm.username', '=', 'a.username')
            ->select([
                'm.id',
                'm.username',
                'm.price as amount',
                'm.type',
                'm.package_name',
                'm.status',
                'm.created_at',
                'a.email'
            ])
            ->where('m.status', '!=', 'cancelled')
            ->whereBetween('m.created_at', [$startDate, $endDate])
            ->get();

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
                    number_format($transaction->amount),
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

            $coinTotal = $coinTransactions->sum('amount');
            $cardTotal = $cardTransactions->sum('amount');
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
        $characters = DB::table('t_roles as c')
            ->leftJoin('game_accounts as a', 'c.userid', '=', 'a.id')
            ->select('c.*', 'a.username', 'a.email')
            ->whereBetween('c.regtime', [$startDate, $endDate])
            ->where('c.isdel', 0)
            ->orderBy('c.level', 'desc')
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
            fputcsv($file, ['Character ID', 'Tên nhân vật', 'Username', 'Email', 'Level', 'Server', 'Nghề nghiệp', 'Tiền', 'Ngày tạo', 'Lần cuối online'], ';');

            foreach ($characters as $character) {
                fputcsv($file, [
                    $character->rid,
                    $character->rname,
                    $character->username ?? 'N/A',
                    $character->email ?? 'N/A',
                    $character->level,
                    'Server ' . $character->serverid,
                    $character->occupation,
                    number_format($character->money),
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
        $monthlyCards = DB::table('monthly_cards as m')
            ->leftJoin('game_accounts as a', 'm.username', '=', 'a.username')
            ->leftJoin('admin_users as admin', 'm.created_by', '=', 'admin.id')
            ->select([
                'm.*',
                'a.email',
                'a.full_name',
                'a.vip_level',
                'admin.username as created_by_username'
            ])
            ->whereBetween('m.created_at', [$startDate, $endDate])
            ->orderBy('m.created_at', 'desc')
            ->get();

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
