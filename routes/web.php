<?php

use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Default route - redirect to User Site
Route::get('/', function () {
	return redirect('/user');
});



// User Site Routes
Route::group(['prefix' => 'user', 'middleware' => 'web'], function () {
	// Authentication routes (no middleware)
	Route::get('/login', [App\Http\Controllers\User\AuthController::class, 'showLogin'])->name('user.login');
	Route::post('/login', [App\Http\Controllers\User\AuthController::class, 'login'])->name('user.login.post');
	Route::get('/register', [App\Http\Controllers\User\AuthController::class, 'showRegister'])->name('user.register');
	Route::post('/register', [App\Http\Controllers\User\AuthController::class, 'register'])->name('user.register.post');
	Route::get('/forgot-password', [App\Http\Controllers\User\AuthController::class, 'showForgotPassword'])->name('user.forgot-password');
	Route::post('/forgot-password', [App\Http\Controllers\User\AuthController::class, 'forgotPassword'])->name('user.forgot-password.post');
	Route::post('/logout', [App\Http\Controllers\User\AuthController::class, 'logout'])->name('user.logout');

	// Protected user routes
	Route::middleware('user.auth')->group(function () {
		// Dashboard
		Route::get('/', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard');
		Route::get('/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('user.dashboard.alt');
		Route::get('/api/quick-stats', [App\Http\Controllers\User\DashboardController::class, 'getQuickStats'])->name('user.api.quick-stats');

		// Recharge routes
		Route::get('/recharge', [App\Http\Controllers\User\RechargeController::class, 'index'])->name('user.recharge');
		Route::post('/recharge/card', [App\Http\Controllers\User\RechargeController::class, 'cardRecharge'])->name('user.recharge.card');
		Route::post('/recharge/bank', [App\Http\Controllers\User\RechargeController::class, 'bankTransfer'])->name('user.recharge.bank');
		Route::get('/recharge/history', [App\Http\Controllers\User\RechargeController::class, 'history'])->name('user.recharge.history');
		Route::get('/recharge/{id}', [App\Http\Controllers\User\RechargeController::class, 'show'])->name('user.recharge.show');

		// Withdraw routes
		Route::get('/withdraw', [App\Http\Controllers\User\WithdrawController::class, 'index'])->name('user.withdraw');
		Route::post('/withdraw', [App\Http\Controllers\User\WithdrawController::class, 'withdraw'])->name('user.withdraw.post');
		Route::get('/withdraw/history', [App\Http\Controllers\User\WithdrawController::class, 'history'])->name('user.withdraw.history');
		Route::get('/withdraw/{id}', [App\Http\Controllers\User\WithdrawController::class, 'show'])->name('user.withdraw.show');

		// Giftcode routes
		Route::get('/giftcode', [App\Http\Controllers\User\GiftcodeController::class, 'index'])->name('user.giftcode');
		Route::post('/giftcode/redeem', [App\Http\Controllers\User\GiftcodeController::class, 'redeem'])->name('user.giftcode.redeem');
		Route::get('/giftcode/history', [App\Http\Controllers\User\GiftcodeController::class, 'history'])->name('user.giftcode.history');
		Route::get('/giftcode/active', [App\Http\Controllers\User\GiftcodeController::class, 'getActiveGiftcodes'])->name('user.giftcode.active');

		// Profile routes (will be implemented later)
		Route::get('/profile', function () {
			return view('user.profile.index');
		})->name('user.profile');
	});
});

// Admin Panel SPA Route
Route::get('/test', function () {
	return view('test');
});

// Admin Authentication Routes (no middleware)
Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Admin Protected Routes (with middleware)
Route::group(['middleware' => ['web', 'admin']], function () {
	// Dashboard
	Route::get('/admin/dashboard', [App\Http\Controllers\Admin\AuthController::class, 'dashboard'])->name('admin.dashboard');

	// Admin User Management Routes (Temporarily disabled)
	// Route::get('/admin/admin-users', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.admin-users.index');
	// Route::get('/admin/admin-users/create', [App\Http\Controllers\Admin\AdminUserController::class, 'create'])->name('admin.admin-users.create');
	// Route::post('/admin/admin-users', [App\Http\Controllers\Admin\AdminUserController::class, 'store'])->name('admin.admin-users.store');
	// Route::get('/admin/admin-users/{id}', [App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.admin-users.show');
	// Route::get('/admin/admin-users/{id}/edit', [App\Http\Controllers\Admin\AdminUserController::class, 'edit'])->name('admin.admin-users.edit');
	// Route::post('/admin/admin-users/{id}/update', [App\Http\Controllers\Admin\AdminUserController::class, 'update'])->name('admin.admin-users.update');
	// Route::post('/admin/admin-users/{id}/toggle-status', [App\Http\Controllers\Admin\AdminUserController::class, 'toggleStatus'])->name('admin.admin-users.toggle-status');

	// Admin Account Management Routes
	Route::get('/admin/accounts', [App\Http\Controllers\Admin\AccountController::class, 'index'])->name('admin.accounts.index');
	Route::get('/admin/accounts/{id}', [App\Http\Controllers\Admin\AccountController::class, 'show'])->name('admin.accounts.show');
	Route::get('/admin/accounts/{id}/edit', [App\Http\Controllers\Admin\AccountController::class, 'edit'])->name('admin.accounts.edit');
	Route::post('/admin/accounts/{id}/update', [App\Http\Controllers\Admin\AccountController::class, 'update'])->name('admin.accounts.update');
	Route::post('/admin/accounts/{id}/ban', [App\Http\Controllers\Admin\AccountController::class, 'ban'])->name('admin.accounts.ban');
	Route::post('/admin/accounts/{id}/unban', [App\Http\Controllers\Admin\AccountController::class, 'unban'])->name('admin.accounts.unban');

	// Game Money Management Routes
	Route::get('/admin/game-money', [App\Http\Controllers\Admin\GameMoneyController::class, 'index'])->name('admin.game-money.index');
	Route::get('/admin/game-money/{id}', [App\Http\Controllers\Admin\GameMoneyController::class, 'show'])->name('admin.game-money.show');
	Route::get('/admin/game-money/{id}/edit', [App\Http\Controllers\Admin\GameMoneyController::class, 'edit'])->name('admin.game-money.edit');
	Route::post('/admin/game-money/{id}/update', [App\Http\Controllers\Admin\GameMoneyController::class, 'update'])->name('admin.game-money.update');

	// Admin Character Management Routes
	Route::get('/admin/characters', [App\Http\Controllers\Admin\CharacterController::class, 'index'])->name('admin.characters.index');
	Route::get('/admin/characters/{id}', [App\Http\Controllers\Admin\CharacterController::class, 'show'])->name('admin.characters.show');
	Route::get('/admin/characters/{id}/edit', [App\Http\Controllers\Admin\CharacterController::class, 'edit'])->name('admin.characters.edit');
	Route::post('/admin/characters/{id}/update', [App\Http\Controllers\Admin\CharacterController::class, 'update'])->name('admin.characters.update');
	Route::post('/admin/characters/{id}/ban', [App\Http\Controllers\Admin\CharacterController::class, 'ban'])->name('admin.characters.ban');
	Route::post('/admin/characters/{id}/unban', [App\Http\Controllers\Admin\CharacterController::class, 'unban'])->name('admin.characters.unban');
	Route::delete('/admin/characters/{id}', [App\Http\Controllers\Admin\CharacterController::class, 'destroy'])->name('admin.characters.destroy');

	// Admin Coin Recharge Management Routes
	Route::get('/admin/coin-recharge', [App\Http\Controllers\Admin\CoinRechargeController::class, 'index'])->name('admin.coin-recharge.index');
	Route::get('/admin/coin-recharge/create', [App\Http\Controllers\Admin\CoinRechargeController::class, 'create'])->name('admin.coin-recharge.create');
	Route::post('/admin/coin-recharge', [App\Http\Controllers\Admin\CoinRechargeController::class, 'store'])->name('admin.coin-recharge.store');
	Route::get('/admin/coin-recharge/{id}', [App\Http\Controllers\Admin\CoinRechargeController::class, 'show'])->name('admin.coin-recharge.show');
	Route::get('/admin/coin-recharge/search-account', [App\Http\Controllers\Admin\CoinRechargeController::class, 'searchAccount'])->name('admin.coin-recharge.searchAccount');
	Route::get('/admin/coin-recharge/statistics', [App\Http\Controllers\Admin\CoinRechargeController::class, 'getStatistics'])->name('admin.coin-recharge.statistics');

	// Admin Giftcode Management Routes
	Route::get('/admin/giftcodes', [App\Http\Controllers\Admin\GiftcodeController::class, 'index'])->name('admin.giftcodes.index');
	Route::get('/admin/giftcodes/create', [App\Http\Controllers\Admin\GiftcodeController::class, 'create'])->name('admin.giftcodes.create');
	Route::post('/admin/giftcodes', [App\Http\Controllers\Admin\GiftcodeController::class, 'store'])->name('admin.giftcodes.store');
	Route::get('/admin/giftcodes/{id}', [App\Http\Controllers\Admin\GiftcodeController::class, 'show'])->name('admin.giftcodes.show');
	Route::get('/admin/giftcodes/{id}/edit', [App\Http\Controllers\Admin\GiftcodeController::class, 'edit'])->name('admin.giftcodes.edit');
	Route::post('/admin/giftcodes/{id}/update', [App\Http\Controllers\Admin\GiftcodeController::class, 'update'])->name('admin.giftcodes.update');
	Route::delete('/admin/giftcodes/{id}', [App\Http\Controllers\Admin\GiftcodeController::class, 'destroy'])->name('admin.giftcodes.destroy');
	Route::post('/admin/giftcodes/{id}/toggle-status', [App\Http\Controllers\Admin\GiftcodeController::class, 'toggleStatus'])->name('admin.giftcodes.toggle-status');

	// Admin Analytics Routes
	Route::get('/admin/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');
	Route::get('/admin/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('admin.analytics.export');

	// Admin IP Management Routes
	Route::get('/admin/ip-management', [App\Http\Controllers\Admin\IpManagementController::class, 'index'])->name('admin.ip-management.index');
	Route::get('/admin/ip-management/banned', [App\Http\Controllers\Admin\IpManagementController::class, 'bannedIps'])->name('admin.ip-management.banned');
	Route::get('/admin/ip-management/suspicious', [App\Http\Controllers\Admin\IpManagementController::class, 'suspicious'])->name('admin.ip-management.suspicious');
	Route::get('/admin/ip-management/{ip}', [App\Http\Controllers\Admin\IpManagementController::class, 'show'])->name('admin.ip-management.show');
	Route::post('/admin/ip-management/{ip}/ban', [App\Http\Controllers\Admin\IpManagementController::class, 'banIp'])->name('admin.ip-management.ban');
	Route::post('/admin/ip-management/{ip}/unban', [App\Http\Controllers\Admin\IpManagementController::class, 'unbanIp'])->name('admin.ip-management.unban');
	Route::get('/admin/ip-management/export', [App\Http\Controllers\Admin\IpManagementController::class, 'export'])->name('admin.ip-management.export');

	// Admin System Routes
	Route::get('/admin/system/performance', [App\Http\Controllers\Admin\SystemController::class, 'performance'])->name('admin.system.performance');
	Route::post('/admin/system/clear-cache', [App\Http\Controllers\Admin\SystemController::class, 'clearCache'])->name('admin.system.clear-cache');
	Route::get('/admin/system/logs', [App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('admin.system.logs');

	// Admin Logs Routes (Legacy)
	Route::get('/admin/logs', [App\Http\Controllers\Admin\AdminLogsController::class, 'index'])->name('admin.logs.index');
	Route::get('/admin/logs/{id}', [App\Http\Controllers\Admin\AdminLogsController::class, 'show'])->name('admin.logs.show');
	Route::get('/admin/logs/export', [App\Http\Controllers\Admin\AdminLogsController::class, 'export'])->name('admin.logs.export');
	Route::get('/admin/logs/statistics', [App\Http\Controllers\Admin\AdminLogsController::class, 'statistics'])->name('admin.logs.statistics');
	Route::get('/admin/logs/login-logs', [App\Http\Controllers\Admin\AdminLogsController::class, 'loginLogs'])->name('admin.logs.login-logs');

	// Admin Monthly Cards Routes
	Route::get('/admin/monthly-cards', [App\Http\Controllers\Admin\MonthlyCardController::class, 'index'])->name('admin.monthly-cards.index');
	Route::get('/admin/monthly-cards/create', [App\Http\Controllers\Admin\MonthlyCardController::class, 'create'])->name('admin.monthly-cards.create');
	Route::post('/admin/monthly-cards', [App\Http\Controllers\Admin\MonthlyCardController::class, 'store'])->name('admin.monthly-cards.store');
	Route::get('/admin/monthly-cards/{id}', [App\Http\Controllers\Admin\MonthlyCardController::class, 'show'])->name('admin.monthly-cards.show');
	Route::post('/admin/monthly-cards/{id}/extend', [App\Http\Controllers\Admin\MonthlyCardController::class, 'extend'])->name('admin.monthly-cards.extend');
	Route::post('/admin/monthly-cards/{id}/cancel', [App\Http\Controllers\Admin\MonthlyCardController::class, 'cancel'])->name('admin.monthly-cards.cancel');
	Route::get('/admin/monthly-cards/search-account', [App\Http\Controllers\Admin\MonthlyCardController::class, 'searchAccount'])->name('admin.monthly-cards.search-account');
	Route::get('/admin/monthly-cards/statistics', [App\Http\Controllers\Admin\MonthlyCardController::class, 'statistics'])->name('admin.monthly-cards.statistics');

	// Admin Battle Pass Routes
	Route::get('/admin/battle-pass', [App\Http\Controllers\Admin\BattlePassController::class, 'index'])->name('admin.battle-pass.index');
	Route::get('/admin/battle-pass/create', [App\Http\Controllers\Admin\BattlePassController::class, 'create'])->name('admin.battle-pass.create');
	Route::post('/admin/battle-pass', [App\Http\Controllers\Admin\BattlePassController::class, 'store'])->name('admin.battle-pass.store');
	Route::get('/admin/battle-pass/{id}', [App\Http\Controllers\Admin\BattlePassController::class, 'show'])->name('admin.battle-pass.show');
	Route::post('/admin/battle-pass/{id}/extend', [App\Http\Controllers\Admin\BattlePassController::class, 'extend'])->name('admin.battle-pass.extend');
	Route::post('/admin/battle-pass/{id}/cancel', [App\Http\Controllers\Admin\BattlePassController::class, 'cancel'])->name('admin.battle-pass.cancel');
	Route::get('/admin/battle-pass/search-account', [App\Http\Controllers\Admin\BattlePassController::class, 'searchAccount'])->name('admin.battle-pass.search-account');
});

// Redirect /admin to login page
Route::get('/admin', function () {
	// Check if user is logged in
	if (Session::has('admin_user')) {
		return redirect('/admin/dashboard');
	}
	return redirect('/admin/login');
});



Route::get('/403', 'AdminCP\AuthController@handle403')->name('403error');
Route::group(['middleware' => 'web', 'namespace' => 'UserCP', 'prefix' => 'UserCP'], function () {

	Route::get('napthe', 'TrumtheController@napthe')->name('napthe');
	Route::post('gachthe', 'TrumtheController@gachthe')->name('gachthe')->middleware('logging_all');

	Route::post('/checkcartdelay', 'CardDelayController@checkCard')->name('check_card_delay')->middleware('logging_all');

	Route::get('/', 'HomeController@index')->name('userDas');
	Route::get('/login', 'AuthController@index')->name('user_login');
	Route::get('/logout', 'HomeController@logout')->name('logout');
	Route::get('/loginios', 'HomeController@loginIos')->name('userloginios');
	Route::get('/forgot', 'AuthController@forgot')->name('user_forgot');
	Route::get('/register', 'AuthController@register')->name('user_register');
	Route::get('/account', 'AccountController@index')->name('account');
	Route::get('/exchange', 'ExchangeController@index')->name('exchange');
	Route::get('/card_month', 'CardMonthController@index')->name('card_month');
	Route::get('/giftcode', 'GiftcodeController@index')->name('giftcode');
	Route::get('/history', 'HistoryController@index')->name('history');
	Route::get('/lottery', 'LotteryController@index')->name('lottery');
	Route::get('/ranks', 'RanksController@index')->name('ranks');
	Route::get('/ranks_reward', 'RanksController@reward')->name('ranks_reward');

	Route::get('/ranksnew/{id}', 'RanksController@ranknew')->name('ranknew');
	Route::get('/ranksnew/reward/{id}', 'RanksController@rewardNew')->name('ranknew_reward');

	// Route::get('/atmPay', 'RechargeController@AtmPay')->name('atmpay');
	// Route::get('/atmPayOk', 'RechargeController@atmPayOk')->name('atmPayOk');
	// Route::get('/recharge/mobi', 'RechargeController@automobi')->name('automobi');
	// Route::post('/recharge/mobi', 'RechargeController@automobi_post')->name('automobi_post');



	// Route::get('/cancelPay', 'RechargeController@cancelPay')->name('cancelPay');
	// Route::get('/info_atm', 'RechargeController@atm_info_page')->name('info_atm');
	route::get('/webshop/log', 'WebshopController@logs')->name('webshoplogs');
	Route::get('/reward_custom', 'RewardCustomController@index')->name('ucp_reward_custon');
	Route::get('/reward_custom/get/{id}', 'RewardCustomController@getReward')->name('ucp_reward_custon_get');
	// Route::get('/paypal', 'RechargeController@PayPal_index')->name('paypal_index');
	// Route::get('/paypal/succses','RechargeController@PayPal_succses')->name('paypal_succses');
	// Route::get('/paypal/error','RechargeController@PayPal_error')->name('paypal_error');
	Route::get('/luckywinwheel', 'EventsController@winwheel')->name('winwheel');



	Route::get('/worldcup', 'WordCupController@index')->name('userwc');
	Route::get('/worldcup/play/{id}', 'WordCupController@playwc')->name('playwc');

	Route::post('/luckywinwheel/reward', 'EventsController@getReward')->name('winwheel_reward')->middleware('logging_all');
	Route::post('/luckywinwheel/checkmoney', 'EventsController@winwheelCheckMoney')->name('winwheel_checkmoney')->middleware('logging_all');
	Route::post('/ranks_reward', 'RanksController@sendmail')->name('ranks_send_item')->middleware('logging_all');

	Route::post('/ranksnew/sendmail', 'RanksController@sendmail2')->name('ranks_new_send_item')->middleware('logging_all');

	Route::post('/worldcup/play', 'WordCupController@playwc_pool')->name('playwc_pool')->middleware('logging_all');

	Route::post('/login', 'AuthController@login')->name('login_post');
	Route::post('/register', 'AuthController@CreateAcc')->name('register_post');
	Route::post('/forgot', 'AuthController@forgot_pro')->name('forgot_post');
	// Route::post('/atmPay','RechargeController@checkAtmPay')->name('atmpay_post');
	Route::post('/oissvselect', 'HomeController@loginIos_selectsv')->name('ios_select_sv');
	Route::post('/reward_custom/getreward', 'RewardCustomController@sendmail')->name('ucp_reward_custon_get_post')->middleware('logging_all');

	//Route::resource('/webshop', 'WebshopController');
	Route::resource('/reward', 'RewardController');
	Route::resource('/recharge', 'RechargeController');
	Route::resource('/banking', 'BankingController');
	Route::resource('/nhapcode', 'nhapcodeController');
	Route::resource('/ruthoahong', 'RutHoaHongController');

	Route::post('/chartreturn', 'AccountController@chartreturn')->name('chartreturn')->middleware('logging_all');
});


Route::group(['middleware' => 'web', 'namespace' => 'UserCP', 'prefix' => 'apiweb40'], function () {
	Route::get('/', 'AuthController@indexIos')->name('user_login_ios');

	//Route::get('/logout', 'HomeController@logout')->name('logout');
	//Route::get('/loginios', 'HomeController@loginIos')->name('userloginios');
});

// Admin login routes (no middleware)
Route::group(['middleware' => 'web', 'namespace' => 'AdminCP', 'prefix' => '1TL_contropanelMS*&()'], function () {
	Route::get('/login', 'AuthController@index')->name('auth.index');
	Route::post('/login', 'AuthController@store')->name('auth.store');
	Route::get('/logout', 'AuthController@logout')->name('auth.logout');
	Route::get('/403', 'AuthController@handle403')->name('403error');
});

// Admin protected routes
Route::group(['middleware' => ['web', 'admin'], 'namespace' => 'AdminCP', 'prefix' => '1TL_contropanelMS*&()'], function () {

	Route::get('/accounts', 'TRewardCustonRidController@api_find_account')->name('api_get_user');
	Route::get('/winwheel/settings/create', 'SettingsController@settingWinWheel')->name('settingsWinwheel');
	Route::get('/winwheel/settings', 'SettingsController@index_settings')->name('index_settings');
	Route::get('/winwheel/settings/delete/{id}', 'SettingsController@settingWinWheel_delete')->name('settingWinWheel_delete');

	Route::get('/winwheel/settings/use/{id}', 'SettingsController@settingWinWheel_use')->name('settingWinWheel_use');

	Route::get('/winwheel/settings/update/{id}', 'SettingsController@settingWinWheel_update')->name('settingWinWheel_update');


	Route::get('/winwheel/settings/update/{id}', 'SettingsController@settingWinWheel_update')->name('settingWinWheel_update');

	Route::get('/gift_code/use/index', 'GiftcodeController@createGiftUseIndex')->name('createGiftUseIndex');
	Route::get('/gift_code/use/update/{id}', 'GiftcodeController@createGiftUse_update')->name('createGiftUse_update');
	Route::get('/gift_code/use/delete/{id}', 'GiftcodeController@createGiftUse_delete')->name('createGiftUse_delete');

	Route::get('/gift_code/use', 'GiftcodeController@createGiftUse')->name('createGiftUse');
	Route::post('/gift_code/use', 'GiftcodeController@createGiftUse_post')->name('createGiftUse_post')->middleware('logging_all');


	Route::post('/winwheel/settings/post', 'SettingsController@settingWinWheel_post')->name('settingsWinwheel_post');

	Route::get('/', 'DashboardController@index')->name('dashboard');
	Route::get('/webshop/cat', 'WebshopCatController@index')->name('webshop_Cat_index');
	Route::get('/webshop/cat/create', 'WebshopCatController@create')->name('webshop_Cat_create');

	Route::post('/webshop/cat', 'WebshopCatController@store')->name('webshop_Cat_post');
	Route::get('/webshop/cat/delete/{id}', 'WebshopCatController@destroy')->name('webshop_Cat_delete');
	Route::get('/webshop/cat/edit/{id}', 'WebshopCatController@edit')->name('webshop_Cat_edit');

	Route::get('/worldcup', 'WordCupController@index')->name('adminwc');
	Route::get('/worldcup/createpool', 'WordCupController@createPool')->name('createpool');
	Route::get('/worldcup/editpool/{id}', 'WordCupController@editPool')->name('editpool');
	Route::get('/worldcup/delpool/{id}', 'WordCupController@deletePool')->name('deletePool');
	Route::get('/worldcup/view/{id}', 'WordCupController@viewuserbypool')->name('poolbyuser');

	Route::post('/worldcup/createpool_submit', 'WordCupController@createpool_submit')->name('createpool_post')->middleware('logging_all');
	Route::post('/worldcup/editpool_post', 'WordCupController@editPool_submit')->name('editPool_post')->middleware('logging_all');


	Route::post('/webshop/cat/edit', 'WebshopCatController@edit_post')->name('webshop_Cat_edit_post')->middleware('logging_all');

	Route::group(['middleware' => 'logging_post'], function () {
		Route::resource('/acc_info', 'AccountController');
		Route::resource('/char_info', 'CharacterController');
		Route::resource('/recharge_info', 'RechargeController');
		Route::resource('/webshop', 'WebshopController');
		Route::resource('/gift_code', 'GiftcodeController');
		Route::resource('/search_item', 'SearchItemController');
		Route::resource('/announce', 'AnnounceController');
		Route::resource('/revenue', 'RevenueController');
		Route::resource('/log', 'LogController');
		Route::resource('/online', 'OnlineController');
		Route::resource('/settings', 'SettingsController');
		Route::resource('/server', 'ServerController');
		Route::resource('/payment_info', 'PaymentController');

		Route::resource('/usergroup', 'UsergroupController');
		Route::resource('/auth', 'AuthController');
		Route::resource('/lottery_info', 'LotteryController');
		Route::resource('/ban_ip', 'BanIpController');
		Route::resource('/battle_pass', 'BattlePassController');

		// Battle Pass specific routes
		Route::get('/battle_pass/{season}/rewards', 'BattlePassController@rewards')->name('battle_pass.rewards');
		Route::post('/battle_pass/{season}/rewards', 'BattlePassController@addReward')->name('battle_pass.add_reward');
		Route::delete('/battle_pass/rewards/{reward}', 'BattlePassController@deleteReward')->name('battle_pass.delete_reward');
		Route::get('/battle_pass_users', 'BattlePassController@userProgress')->name('battle_pass.users');
		Route::post('/battle_pass/purchase_premium', 'BattlePassController@purchasePremium')->name('battle_pass.purchase_premium');
		Route::post('/battle_pass/add_exp', 'BattlePassController@addExp')->name('battle_pass.add_exp');
	});
	Route::get('/reward_custon', 'TRewardCustonController@index')->name('reward_custon');
	Route::get('/reward_custon/create-reward/{id}', 'TRewardCustonController@createReward')->name('create-reward');

	Route::get('/reward_custon/delete/{id}', 'TRewardCustonController@delete')->name('reward_custon_delete');
	Route::get('/reward_custon/edit/{id}', 'TRewardCustonController@edit')->name('reward_custon_edit');


	Route::get('/reward_custon/create', 'TRewardCustonController@create')->name('reward_custon_create');
	Route::get('/reward_custon/edit/{id}', 'TRewardCustonController@edit')->name('reward_custon_edit');

	Route::get('/reward_custon/list/rid', 'TRewardCustonRidController@list_rid')->name('reward_custon_list_rid');
	Route::get('/reward_custon/list/rid/{id}', 'TRewardCustonRidController@delete')->name('reward_custon_delete_rid');

	Route::post('/reward_custon/create', 'TRewardCustonController@create_post')->name('reward_custon_post')->middleware('logging_all');

	Route::get('/reward_custon/{id}/create', 'TRewardCustonRidController@create')->name('reward_custon_add_rid');
	Route::post('/reward_custon/{id}/create', 'TRewardCustonRidController@create_post')->name('reward_custon_add_rid_post')->middleware('logging_all');
	Route::post('/reward_custon/edit', 'TRewardCustonController@edit_post')->name('reward_custon_edit_post')->middleware('logging_all');

	Route::get('/winwheel/log', 'LogController@winwheelLog')->name('winwheelLog');


	Route::get('/ranks', 'RanksNewController@index')->name('ranks_das');
	Route::get('/ranks/create', 'RanksNewController@createNew')->name('ranks_create');
	Route::get('/ranks/update/{id}', 'RanksNewController@updaterank')->name('ranks_update');

	Route::get('/ranks/delete/{id}', 'RanksNewController@delete_rank')->name('ranks_delte');

	Route::post('/ranks/create', 'RanksNewController@createNew_post')->name('createNew_post')->middleware('logging_all');
	Route::post('/ranks/update/{id}', 'RanksNewController@updaterank_post')->name('ranks_update__post')->middleware('logging_all');
});

Route::group(['middleware' => 'web', 'namespace' => 'Api', 'prefix' => 'api'], function () {
	Route::get('/init', 'AuthController@init');
	Route::get('/recharge', 'RechargeController@recharge');
	// Route::post('/recharge', 'RechargeController@recharge')->middleware('logging_all');
	Route::post('/register', 'AuthController@register')->middleware('logging_all');
	Route::post('/login', 'AuthController@login');
	Route::post('/ios/proxy/apple/getOrderi/info', 'AuthController@getOrder');
	Route::post('/update_info', 'UserCPController@update_info')->middleware('logging_all');
	Route::post('/change_password', 'UserCPController@change_password')->middleware('logging_all');
	Route::post('/update_secret', 'UserCPController@update_secret')->middleware('logging_all');
	Route::post('/exchange', 'UserCPController@exchange')->middleware('logging_all');
	Route::post('/giftcode', 'UserCPController@giftcode')->middleware('logging_all');
	Route::post('/webshop', 'UserCPController@webshop')->middleware('logging_all');
	Route::post('/card_month', 'UserCPController@card_month')->middleware('logging_all');
	Route::post('/lottery', 'UserCPController@lottery')->middleware('logging_all');
	Route::get('/forgot', 'AuthController@resetpass');
	// Route::get('/1addcoin', 'AuthController@AddCoin')->middleware('logging_all');
	Route::get('/ios_login', 'AuthController@ios_login')->name('ios_login');
});
