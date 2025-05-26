<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('process-trumthe-123123123azc', 'UserCP\TrumtheController@process')->middleware('logging_all');

// Admin API Routes
Route::prefix('admin')->group(function () {
    // Authentication
    Route::post('/login', 'Api\AdminAuthController@login');
    Route::post('/logout', 'Api\AdminAuthController@logout')->middleware('auth:sanctum');
    Route::get('/user', 'Api\AdminAuthController@user')->middleware('auth:sanctum');

    // Protected admin routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', 'Api\DashboardController@stats');

        // Account Management
        Route::get('/accounts/search', 'Api\AccountController@search');
        Route::get('/accounts/{id}', 'Api\AccountController@show');
        Route::put('/accounts/{id}', 'Api\AccountController@update');
        Route::post('/accounts/{id}/ban', 'Api\AccountController@ban');
        Route::post('/accounts/{id}/unban', 'Api\AccountController@unban');
        Route::post('/accounts/{id}/add-coin', 'Api\AccountController@addCoin');

        // Character Management
        Route::get('/characters/search', 'Api\CharacterController@search');
        Route::get('/characters/{id}', 'Api\CharacterController@show');
        Route::post('/characters/{id}/ban', 'Api\CharacterController@ban');
        Route::post('/characters/{id}/unban', 'Api\CharacterController@unban');
        Route::delete('/characters/{id}', 'Api\CharacterController@destroy');
        Route::put('/characters/{id}/stats', 'Api\CharacterController@updateStats');
        Route::get('/characters/{id}/inventory', 'Api\CharacterController@inventory');
        Route::post('/characters/{id}/send-items', 'Api\CharacterController@sendItems');

        // Coin Recharge
        Route::get('/recharge/history', 'Api\AdminRechargeController@index');
        Route::post('/recharge/manual', 'Api\AdminRechargeController@store');
        Route::get('/recharge/statistics', 'Api\AdminRechargeController@statistics');
        Route::get('/recharge/{id}', 'Api\AdminRechargeController@show');
        Route::put('/recharge/{id}', 'Api\AdminRechargeController@update');
        Route::post('/recharge/{id}/approve', 'Api\AdminRechargeController@approve');
        Route::post('/recharge/{id}/reject', 'Api\AdminRechargeController@reject');

        // Monthly Card
        Route::get('/monthly-cards', 'Api\MonthlyCardController@index');
        Route::post('/monthly-cards/purchase', 'Api\MonthlyCardController@purchase');

        // Battle Pass
        Route::get('/battle-pass/seasons', 'Api\BattlePassController@index');
        Route::post('/battle-pass/seasons', 'Api\BattlePassController@store');
        Route::get('/battle-pass/seasons/{id}', 'Api\BattlePassController@show');
        Route::put('/battle-pass/seasons/{id}', 'Api\BattlePassController@update');
        Route::delete('/battle-pass/seasons/{id}', 'Api\BattlePassController@destroy');
        Route::post('/battle-pass/seasons/{id}/rewards', 'Api\BattlePassController@addReward');
        Route::delete('/battle-pass/rewards/{id}', 'Api\BattlePassController@deleteReward');
        Route::get('/battle-pass/user-progress', 'Api\BattlePassController@userProgress');
        Route::post('/battle-pass/purchase-premium', 'Api\BattlePassController@purchasePremium');
        Route::post('/battle-pass/add-exp', 'Api\BattlePassController@addExp');

        // IP Management
        Route::get('/ip-management', 'Api\IPController@index');
        Route::post('/ip-management', 'Api\IPController@store');
        Route::get('/ip-management/dashboard', 'Api\IPController@dashboard');
        Route::get('/ip-management/lookup', 'Api\IPController@lookup');
        Route::get('/ip-management/{id}', 'Api\IPController@show');
        Route::put('/ip-management/{id}', 'Api\IPController@update');
        Route::delete('/ip-management/{id}', 'Api\IPController@destroy');
        Route::post('/ip-management/{id}/unban', 'Api\IPController@unban');

        // Giftcode Management
        Route::get('/giftcodes', 'Api\GiftcodeController@index');
        Route::post('/giftcodes', 'Api\GiftcodeController@store');
        Route::get('/giftcodes/{id}', 'Api\GiftcodeController@show');
        Route::put('/giftcodes/{id}', 'Api\GiftcodeController@update');
        Route::delete('/giftcodes/{id}', 'Api\GiftcodeController@destroy');
        Route::get('/giftcodes/{id}/usage', 'Api\GiftcodeController@usage');
        Route::get('/giftcodes/{id}/export', 'Api\GiftcodeController@export');

        // Servers
        Route::get('/servers', 'Api\ServerController@index');
    });
});
