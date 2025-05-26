<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\ExchangeRequest;
use App\Http\Requests\AccountRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\SecretRequest;
use App\Http\Requests\GiftcodeRequest;
use App\Http\Requests\LotteryRequest;
use App\Http\Requests\WebshopRequest;


use App\Login;
use App\Role;
use App\Giftcode;
use App\Webshop;
use App\Giftcode_Log;
use App\Webshop_Log;
use App\History;
use App\Recharge;
use App\Lottery;

use App\Account;
use App\Server;
use DB;

use Auth;
use Config;
use Helper;
use Hash;
use Response;
use Illuminate\Support\Str;

use Carbon\Carbon;

class UserCPController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function update_info(AccountRequest $request)
	{
		$account = Auth::user();

		if ($account->code_secret !== NULL && Hash::check($request->secret, $account->code_secret) === FALSE) {

			return Response::json(['message' => __('usercp.message_secret_failed'), 'status' => 0]);
		} else {

			$phone = $request->phone;
			$email = $request->email;

			$account->Phone = $phone;
			$account->Email = $email;
			$account->save();

			History::create([
				'uid' => $account->UserID,
				'type' => 1,
				'content' => [
					'phone' => $account->phone,
					'email' => $account->email,
				]
			]);
		}

		return Response::json(['message' => __('usercp.message_update_info'), 'status' => 1]);
	}


	public function update_referrals(AccountRequest $request)
	{
		$account = Auth::user();

		if ($account->code_secret !== NULL && Hash::check($request->secret, $account->code_secret) === FALSE) {

			return Response::json(['message' => __('usercp.message_secret_failed'), 'status' => 0]);
		} else {

			$phone = $request->phone;
			$email = $request->email;

			$account->Phone = $phone;
			$account->Email = $email;
			$account->save();

			History::create([
				'uid' => $account->UserID,
				'type' => 1,
				'content' => [
					'phone' => $account->phone,
					'email' => $account->email,
				]
			]);
		}

		return Response::json(['message' => __('usercp.message_update_info'), 'status' => 1]);
	}

	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function change_password(PasswordRequest $request)
	{

		$account = Auth::user();

		if ($account->code_secret !== NULL && Hash::check($request->secret, $account->code_secret) === FALSE) {

			return Response::json(['message' => __('usercp.message_secret_failed'), 'status' => 0, 'type' => 1]);
		} elseif ($account->code_secret === NULL && md5($request->old_password) != $account->Password) {
			return Response::json(['message' => __('usercp.message_password_failed' . $request->old_password), 'status' => 0, 'type' => 2]);
		} else {
			$password = $request->password;

			$account->Password = md5($password);

			$account->save();

			History::create([
				'uid' => $account->UserID,
				'type' => 2,
				'content' => [
					'password' => $request->old_password,
				]
			]);
			Auth::logout();
			return Response::json(['message' => __('usercp.message_password_success'), 'status' => 1]);
		}
	}

	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function update_secret(SecretRequest $request)
	{
		$account = Auth::user();

		if ($account->code_secret === NULL) {

			$code_secret = $request->secret;

			History::create([
				'uid' => $account->UserID,
				'type' => 5,
				'content' => 'Thay đổi mã bảo mật.'
			]);

			$account->code_secret = Hash::make($code_secret);
			$account->save();

			return Response::json(['status' => __('usercp.message_update_secret')]);
		}
	}



	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function exchange(ExchangeRequest $request)
	{
		$balance = $request->amount;
		$rid = $request->ex_rid;
		$account = Auth::user();
		$server = Server::where('Id', $account->Role)->first();
		$prefix_userid = Config::get('mu.platform.name');

		if ($server) {
			$role = DB::connection($server->Id)->table('t_roles')->where('userid', $prefix_userid . $account->UserID)->orderBy('lasttime', 'desc')->first();
			if (!$role) {
				return Response::json(['message' => __('usercp.message_unknown_char'), 'status' => 0]);
			}
		} else {
			return Response::json(['message' => __('usercp.message_unknown_server'), 'status' => 0]);
		}

		$platform_mode = Config::get('mu.alpha.zoneid');

		if ($account->Money >= $balance || ($account->Role == $platform_mode && Helper::checkDateAlpha() !== FALSE)) {
			$dt = Carbon::now();

			$today = $dt->dayOfWeek;

			$promotion = Config::get('mu.promotion.percent');
			$exchanges = Config::get('mu.exchange');
			$promotion_zoneid = Config::get('mu.promotion.zoneid');
			$promotion_requires = Config::get('mu.promotion.require');
			$promotion_date = Config::get('mu.promotion.date');
			$promotion_weekly = Config::get('mu.promotion.weekly');
			$promotion_limit_number = Config::get('mu.promotion.limit.number');
			$promotion_limit_percent = Config::get('mu.promotion.limit.percent');

			$diamond = History::where('type', 3)->whereDate('created_at', $dt->toDateString())->count();

			$bonus = 0;
			foreach ($exchanges as $i => $exchange) {
				if ($account->Role >= $exchange) {
					$amount = $balance * $i;
				}
			}

			if ($promotion > 0 && $dt >= $promotion_date[0] && $dt <= $promotion_date[1] && ($account->Role == $promotion_zoneid || $promotion_zoneid == 0)) {
				$bonus = $amount * $promotion / 100;
			} elseif ($promotion_weekly[$today] > 0) {
				$bonus = $amount * $promotion_weekly[$today] / 100;
			} elseif ($diamond < $promotion_limit_number) {
				$bonus = $amount * $promotion_limit_percent / 100;
			}

			if (count($promotion_requires) > 0 && $dt >= $promotion_date[0] && $dt <= $promotion_date[1] && ($account->Role == $promotion_zoneid || $promotion_zoneid == 0)) {

				$diamond_logs = History::where('uid', $account->UserID)->where('type', 3)->whereDate('created_at', $dt->toDateString())->get();

				$diamond_times = History::where('uid', $account->UserID)->where('type', 3)->whereDate('created_at', $dt->toDateString())->count();

				$diamond_total = 0;
				foreach ($diamond_logs as $diamond_log) {
					$diamond_total += $diamond_log->content['money'];
				}

				foreach ($promotion_requires as $i => $promotion_require) {

					$diamond_remain = $i - $diamond_times;

					if ($diamond_remain == 1) {
						$diamond_total += $amount;
						$bonus = $diamond_total * $promotion_require / 100;
					}
				}
			}

			$money = $amount + $bonus;

			$server = Server::where('Id', $account->Role)->first();

			$cc = Helper::getGCC($prefix_userid . $account->UserID, $money, $dt);
			$re = DB::connection($server->Id)->table('t_tempmoney')->insert([
				'cc' => $cc,
				'uid' => $prefix_userid . $account->UserID,
				'rid' => 0,
				'addmoney' => $money,
				'itemid' => 0,
				'chargetime' => $dt
			]);
			// dd($re);

			if ($platform_mode < 1 || $account->Role != $platform_mode || ($account->Role == $platform_mode && Helper::checkDateAlpha() === FALSE)) {
				$account->Money = $account->Money - $balance;
				$account->save();
			}

			$order_no = Str::random(32);
			// add chip
			$chip_add = Helper::setChip($request->amount);
			$msg_chip = '';

			if ($chip_add && Config::get('chip.open') == 0) {
				$msg_chip = ' ' . __('usercp.chip.add.message.success', ['amount' => $chip_add, 'name' => config('chip.name')]);
			}

			DB::connection($server->Id)->table('t_inputlog')->insert([
				'amount' => $money,
				'u' => $prefix_userid . $account->UserID,
				'rid' => $rid ? $rid : 0,
				'order_no' => $order_no,
				'cporder_no' => $order_no,
				'time' => $dt->timestamp,
				'sign' => $cc,
				'inputtime' => $dt,
				'result' => 'success',
				'zoneid' => $account->Role,
				'chargetime' => $dt
			]);

			if ($platform_mode < 1 || $account->Role != $platform_mode || ($account->Role == $platform_mode && Helper::checkDateAlpha() === FALSE)) {
				History::create([
					'uid' => $account->UserID,
					'zoneid' => $account->Role,
					'type' => 3,
					'balance' => $account->Money,
					'content' => [
						'money' => $amount,
						'bonus' => $bonus
					]
				]);
			}

			return Response::json([
				'message' => __('usercp.message_exchange_success', ['diamond' => Helper::numberFormat($amount)]) . $msg_chip,
				'balance' => Helper::numberFormat($account->Money),
				'status' => 1
			]);
		} else {

			$balance = abs($account->Money - $balance);

			return Response::json(['message' => __('usercp.message_exchange_failed', ['currency' => Config::get('mu.currency.name'), 'balance' => Helper::numberFormat($balance)]), 'status' => 0]);
		}
	}

	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function giftcode(GiftcodeRequest $request)
	{

		$code = $request->giftcode;
		$code = Helper::str_upper($code);

		$account = Auth::user();
		$rid = $request->ex_rid;
		$server = Server::where('Id', $account->Role)->first();
		if ($server) {
			$role = DB::connection($server->Id)->table('t_roles')->where('rid', $rid)->orderBy('lasttime', 'desc')->first();

			if (!$role) {
				return Response::json(['message' => __('usercp.message_unknown_char'), 'status' => 0]);
			}
		} else {
			return Response::json(['message' => __('usercp.message_unknown_server'), 'status' => 0]);
		}

		$giftcode = Giftcode::where('code', '["' . $code . '"]')->orderBy('id', 'desc')->first();

		if ($giftcode === NULL) {
			return Response::json(['message' => __('usercp.message_giftcode_error'), 'status' => 0]);
		}

		if ($giftcode->type == 2) {
			$accounts = explode(',', $giftcode->accounts);
			if (!in_array($account->UserName, $accounts)) {
				return Response::json(['message' => 'Tài khoản này khoản sử dụng được code này!', 'status' => 0]);
			}
		}

		if ($giftcode->multiple == 1) {
			$check_exist = Giftcode_Log::where('groupid', $giftcode->id)
				->where('uid', $account->UserID)
				->where('zoneid', $account->Role)
				->exists();
		} elseif ($giftcode->type == 1) {
			$check_exist = Giftcode_Log::where('giftcode', $code)
				->where('uid', $account->UserID)
				->where('zoneid', $account->Role)
				->exists();
		} elseif ($giftcode->type == 2) {

			$check_exist = Giftcode_Log::where('giftcode', $code)
				->where('uid', $account->UserID)
				//->where('zoneid', $account->Role)
				->exists();
		} else {
			$check_exist = Giftcode_Log::where('giftcode', $code)
				->where('uid', $account->UserID)
				->where('rid', $role->rid)
				->where('zoneid', $account->Role)
				->exists();
		}

		if ($check_exist !== FALSE) {

			return Response::json(['message' => __('usercp.message_giftcode_used'), 'status' => 0]);
		}

		if ($giftcode->period > 0) {

			$dt = Carbon::parse($giftcode->created_at);
			$date = $dt->addDays($giftcode->period);
			$endOfDay = $date->endOfDay();
			$period = $endOfDay->timestamp;
			if (Carbon::now()->timestamp >= $period) {
				return Response::json(['message' => __('usercp.message_giftcode_expired'), 'status' => 0]);
			}
		}

		if ($giftcode->limit > 0) {
			$giftcode_log = Giftcode_Log::where('giftcode', $code)->count();

			if ($giftcode_log >= $giftcode->limit) {

				return Response::json(['message' => __('usercp.message_giftcode_full'), 'status' => 0]);
			}
		}

		if ($giftcode->zoneid > 0) {
			if ($giftcode->zoneid != $account->Role) {
				return Response::json(['message' => __('usercp.message_giftcode_na'), 'status' => 0]);
			}
		}

		$mailid = DB::connection($server->Id)->table('t_mail')->insertGetId([
			'senderrid' => 0,
			'senderrname' => 'GM',
			'sendtime' => Carbon::now(),
			'receiverrid' => $role->rid,
			'reveiverrname' => $role->rname,
			'subject' => __('usercp.mail_giftcode_subject', ['giftcode' => $code]),
			'content' => __('usercp.mail_giftcode_content', ['char' => $role->rname, 'content' => $giftcode->content])
		]);

		$items = [];
		foreach ($giftcode->items as $item) {

			$goods = explode(',', $item);

			$items[] = [
				'mailid' => $mailid,
				'goodsid' => $goods[0],
				'gcount' => $goods[1],
				'binding' => $goods[2],
				'forge_level' => $goods[3],
				'appendproplev' => $goods[4],
				'lucky' => $goods[5],
				'excellenceinfo' => $goods[6],
			];
		}

		DB::connection($server->Id)->table('t_mailgoods')->insert($items);

		Giftcode_Log::create([
			'uid' => $account->UserID,
			'rid' => $role->rid,
			'zoneid' => $role->zoneid,
			'giftcode' => $code,
			'groupid' => $giftcode->id,
		]);

		return Response::json(['message' => __('usercp.message_giftcode_success'), 'status' => 1]);
	}


	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function webshop(WebshopRequest $request)
	{

		if (Config::get('mu.webshop') != 1) {
			return Response::json(['message' => 'Webshop không mở bán vật phẩm!', 'status' => 0]);
		}

		if (!is_numeric($request->qty)) {

			return Response::json(['message' => __('usercp.webshop.number'), 'status' => 0]);
		}

		$qty = (int)$request->qty;
		if ($qty  == 0) {
			return Response::json(['message' => __('usercp.webshop.min', ['number' => 1]), 'status' => 0]);
		}

		$id = $request->webshop;
		$account = Auth::user();
		$server = Server::where('Id', $account->Role)->first();

		$rid = $request->ex_rid;

		Helper::setRid($rid);

		if ($server) {
			$role = DB::connection($server->Id)->table('t_roles')->where('rid', $rid)->orderBy('lasttime', 'desc')->first();
			if (!$role) {
				return Response::json(['message' => __('usercp.message_unknown_char'), 'status' => 0]);
			}
		} else {
			return Response::json(['message' => __('usercp.message_unknown_server'), 'status' => 0]);
		}

		$webshop = Webshop::where('id', $id)->orderBy('id', 'desc')->first();

		if ($webshop === NULL) {
			return Response::json(['message' => 'Vật phẩm này không được bán!', 'status' => 0]);
		}

		$price = $qty * $webshop->price;

		if ($account->Money < $price) {
			return Response::json(['message' => 'Bạn không đủ coin để mua, vui lòng nạp thêm!', 'status' => 0]);
		}


		$mailid = DB::connection($server->Id)->table('t_mail')->insertGetId([
			'senderrid' => 0,
			'senderrname' => 'GM',
			'sendtime' => Carbon::now(),
			'receiverrid' => $role->rid,
			'reveiverrname' => $role->rname,
			'subject' => 'Mua vật phẩm',
			'content' => 'Bạn mua thành công ' . $qty . ' vật phẩm ' . $webshop->name . ' với giá ' . Helper::numberFormat($price) . ' ' . Config('mu.currency.name')
		]);

		// add chip
		$chip_add = Helper::setChip($price);
		$msg_chip = '';

		if ($chip_add) {
			$msg_chip = ' ' . __('usercp.chip.add.message.success', ['amount' => $chip_add, 'name' => config('chip.name')]);
		}

		$items = [];
		foreach ($webshop->items as $item) {
			$goods = explode(',', $item);

			$items[] = [
				'mailid' => $mailid,
				'goodsid' => $goods[0],
				'gcount' => $qty,
				'binding' => $goods[2],
				'forge_level' => $goods[3],
				'appendproplev' => $goods[4],
				'lucky' => $goods[5],
				'excellenceinfo' => $goods[6],
			];
		}

		DB::connection($server->Id)->table('t_mailgoods')->insert($items);
		$account->Money = $account->Money - $price;
		$account->save();

		Webshop_Log::create([
			'uid' => $account->UserID,
			'rid' => $role->rid,
			'zoneid' => $role->zoneid,
			'item' => $webshop->item,
			'price' => $price,
			'qty' => $qty
		]);

		return Response::json(['message' => __('usercp.webshop.msg.success', [
			'qty' => $qty,
			'name' => $webshop->name,
			'price' => Helper::numberFormat($price),
			'coin' => Config('mu.currency.name')

		]) . $msg_chip, 'status' => 1]);
	}

	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function card_month(Request $request)
	{

		if ($request->has('card_month')) {

			$amount = Config::get('mu.card_month.money');
			$rmb = Config::get('mu.card_month.rmb');

			$platform_mode = Config::get('mu.alpha.zoneid');

			$account = Auth::user();

			$server = Server::where('Id', $account->Role)->first();

			if ($server) {
				$role = DB::connection($server->Id)->table('t_roles')->where('userid', $account->UserID)->orderBy('lasttime', 'desc')->first();

				if (!$role) {
					return Response::json(['message' => __('usercp.message_unknown_char'), 'status' => 0]);
				}
			} else {
				return Response::json(['message' => __('usercp.message_unknown_server'), 'status' => 0]);
			}

			if ($account->Money >= $amount || ($account->Role == $platform_mode && Helper::checkDateAlpha() !== FALSE)) {

				$server = Server::where('Id', $account->Role)->first();

				$dt = Carbon::now();

				$cc = Helper::getGCC($account->UserID, $rmb, $dt);

				DB::connection($server->Id)->table('t_tempmoney')->insert([
					'cc' => $cc,
					'uid' => $account->UserID,
					'rid' => 0,
					'addmoney' => $rmb,
					'itemid' => 0,
					'chargetime' => $dt
				]);

				if ($platform_mode < 1 || $account->Role != $platform_mode || ($account->Role == $platform_mode && Helper::checkDateAlpha() === FALSE)) {
					$account->Money = $account->Money - $amount;
					$account->save();

					History::create([
						'uid' => $account->UserID,
						'zoneid' => $account->Role,
						'type' => 4,
						'balance' => $account->Money,
						'content' => [
							'money' => $amount
						]
					]);
				}

				return Response::json([
					'message' => __('usercp.message_cardmonth_success'),
					'balance' => Helper::numberFormat($account->Money),
					'status' => 1
				]);
			} else {

				$balance = abs($account->Money - $amount);

				return Response::json(['message' => __('usercp.message_exchange_failed', ['currency' => Config::get('mu.currency.name'), 'balance' => Helper::numberFormat($balance)]), 'status' => 0]);
			}
		}
	}



	/**
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function lottery(LotteryRequest $request)
	{
		$type = $request->type;
		$codes = $request->number;
		$amount = $request->amount;

		$number = [];
		foreach ($codes as $code) {
			$code = abs(intval($code));
			$number[] = vsprintf("%02s", $code);
		}

		$update_time = Config::get('lottery.update_time');
		$limit_number = Config('lottery.limit');

		$record_time = $update_time - 2;

		$account = Auth::user();

		$money = $amount * 1000;

		$balance = abs($account->Money - $money);

		$dt = Carbon::now();

		if ($type < 3) {

			$check_l_t = Lottery::select('code')->where('uid', $account->UserID)->where('type', $type)->whereraw("date(created_at) = CURDATE()")->get();
			if ($check_l_t) {
				$list_code = [];
				foreach ($check_l_t as $key => $value) {
					$list_code[] = $value->code[0];
				}
				if (count($list_code) > $limit_number) {
					return Response::json(['message' => 'Bạn đánh quá nhiều rồi.', 'status' => 0]);
				}
				if (in_array($request->number[0], $list_code)) {
					return Response::json(['message' => 'Số này đã được ghi', 'status' => 0]);
				}
			}
		}




		if (is_array($number) && !array_filter($number, 'is_numeric')) {
			return Response::json(['message' => __('usercp.message_lottery_invaild'), 'status' => 0]);
		} elseif (is_array($number) && array_unique($number) != $number) {
			return Response::json(['message' => __('usercp.message_lottery_error'), 'status' => 0]);
		} elseif ($dt->hour > $record_time) {
			return Response::json(['message' => __('usercp.message_lottery_expired'), 'status' => 0]);
		} elseif ($account->Money < $money) {
			return Response::json(['message' => __('usercp.message_lottery_failed', ['currency' => Config::get('mu.currency.name'), 'balance' => Helper::numberFormat($balance)]), 'status' => 0]);
		} else {

			Lottery::create([
				'uid' => $account->UserID,
				'type' => $type,
				'code' => $number,
				'amount' => $money
			]);

			$account->Money = $account->Money - $money;
			$account->save();

			if (is_array($number)) {
				$number = implode('-', $number);
			}

			return Response::json([
				'message' => __('usercp.message_lottery_success', ['currency' => Config::get('mu.currency.name'), 'balance' => Helper::numberFormat($money), 'type' => Helper::getLotteryType($type), 'number' => $number]),
				'balance' => Helper::numberFormat($account->Money),
				'status' => 1
			]);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
