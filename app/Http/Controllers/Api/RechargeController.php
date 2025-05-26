<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Recharge;
use App\Account;
use App\Payment;
use Config;
use Carbon\Carbon;

use App\Helper;
use Paymentwall_Config;
use Paymentwall_Pingback;

class RechargeController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */	
    public function recharge(Request $request) {
		
		$gateway = Config::get('recharge.gateway');	
		
		if($gateway == 4 && $request->has('transaction_id') && $request->has('password') && $request->has('real_amount') && $request->has('status')) {
			$transaction_id = $request->input('transaction_id');
			$code = $request->input('password');
			$amount = $request->input('real_amount');
			$amount = intval($amount);
			$status = $request->input('status');
			
			$recharge = Recharge::where('code', $code)->first();
			
			$recharge->serial = $transaction_id;
			
			if($status == 1) {
				$recharge->amount = $amount;
				$recharge->status = 1;
				$recharge->save();

				$exchange = Config::get('mu.recharge.exchange');
			
				$balance = Helper::getBonus($amount);
				
				$promotion = $balance * Config::get('recharge.promotion') / 100;
				
				$account = Account::where('id', $recharge->uid)->first();
			
				$account->balance = $account->balance + $balance + $promotion;
				$account->save();
				
				
				return 'SUCCEED|TOPPED_UP_THB_' . $amount  . '_TO_' . $account->username; 				
				
			} else {				
				$recharge->status = 2;			
				$recharge->save();
				
				return 'ERROR|UNKNOWN'; 				
			}
			
		} elseif($gateway == 5) {

			$id = $request->merchant_id;
			$zoneid = $request->zoneid;
			$type = $request->payment_type;
			$ip = $request->ip;
			
			$paymentwall = Config::get('recharge.paymentwall');
			$public_key = $paymentwall[$id]['public_key'];
			$private_key = $paymentwall[$id]['private_key'];
			$merchant_id = $paymentwall[$id]['merchant_id'];	

			$recharge_promotion = Config::get('mu.recharge.promotion');
			$recharge_date = Config::get('mu.recharge.date');
			$dt = Carbon::now();			
			
			Paymentwall_Config::getInstance()->set([
				'api_type' => Paymentwall_Config::API_VC,
				'public_key' => $public_key,
				'private_key' => $private_key
			]);
			
			$pingback = new Paymentwall_Pingback($request->all(), $request->ip());
			
			if ($pingback->validate()) {
				
				$account = Account::where('userid', $request->uid)->first();
				
				if ($pingback->isDeliverable()) {
					
					$payment = Payment::where('transaction_id', $request->ref)->exists();
					
					if($payment === FALSE) {
						Payment::create([
							'uid' => $account->id,
							'gateway' => $gateway,
							'merchant' => $merchant_id,
							'type' => $type,
							'transaction_id' => $request->ref,
							'amount' => $request->revenue,
							'status' => 1,
							'zoneid' => $zoneid,
							'ip' => $ip,
						]);
						
						$balance = $pingback->getVirtualCurrencyAmount();
					
						$promotion = 0;
						if($recharge_promotion > 0 && $dt >= $recharge_date[0] && $dt <= $recharge_date[1]) {
							$promotion = $balance * $recharge_promotion / 100;
						}
				
						$account->balance = $account->balance + $balance + $promotion;
						$account->save();
					}

				} else if ($pingback->isCancelable()) {
        			Payment::create([
						'uid' => $account->id,
						'gateway' => $gateway,
						'merchant' => $merchant_id,
            			'type' => $type,
           				'transaction_id' => $request->ref,
						'amount' => $request->revenue,
						'status' => 2,
						'zoneid' => $zoneid,
						'ip' => $ip,
        			]);
					
					$balance = $pingback->getVirtualCurrencyAmount();
				
					$promotion = 0;
					if($recharge_promotion > 0 && $dt >= $recharge_date[0] && $dt <= $recharge_date[1]) {
						$promotion = $balance * $recharge_promotion / 100;
					}
			
					$account->balance = $account->balance + $balance - $promotion;
					$account->save();					
				} else if ($pingback->isUnderReview()) {
	        		
					Payment::create([
						'uid' => $account->id,
						'gateway' => $gateway,
						'merchant' => $merchant_id,
            			'type' => $type,
           				'transaction_id' => $request->ref,
						'amount' => $request->revenue,
						'status' => 0,
						'zoneid' => $zoneid,
						'ip' => $ip,
        			]);
				}
				return 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
			} else {
				return $pingback->getErrorSummary();
			}
		} elseif($gateway == 9) {
			
			$uid = $request->uid;
			$transaction_id = $request->id;
			$offer_id = $request->oid;
			$new_currency = $request->new;
			$hash_signature = $request->sig;			
			
			$superrewards = Config::get('recharge.superrewards');
			
			$user_id = decrypt($uid);
			
			$merchant = explode('|', $user_id);
			$id = $merchant[1];			
			
			$merchant_id = $superrewards[$id]['merchant_id'];
			$secret_key = $superrewards[$id]['secret_key'];

			$hash = md5($transaction_id.':'.$new_currency.':'.$uid.':'.$secret_key);
			
			if ($hash == $hash_signature) {
				$account = Account::where('userid', $merchant[0])->first();
				
	        	Payment::create([
					'uid' => $account->id,
					'gateway' => $gateway,
					'merchant' => $merchant_id,
            		'type' => $offer_id,
           			'transaction_id' => $transaction_id,
					'amount' => $new_currency,
					'status' => 1,
					'zoneid' => $account->zoneid,
					'ip' => $account->ip,
        		]);
					
				$balance = $new_currency;
				
				$promotion = $balance * Config::get('recharge.promotion') / 100;
			
				$account->balance = $account->balance + $balance + $promotion;
				$account->save();

				return "1\n";
			} else {
				return "0\n";
			}				
		} elseif($gateway == 16) {
			$recard = Config::get('recharge.recard');
			$recharge_promotion = Config::get('mu.recharge.promotion');
			$recharge_date = Config::get('mu.recharge.date');
			
			$dt = Carbon::now();
			
			$secret_key = $recard[1]['secret_key'];
			
			if($secret_key == $request->secret_key) {
				
				$transaction_code = (string) $request->transaction_code;
				$status = (int) $request->status;
				$amount = (int) $request->amount;
				
				$recharge = Recharge::where('transaction_code', $transaction_code)->where('status', 0)->first();
				
				if($recharge !== null) {
				
					if($status == 1) {
						$recharge->amount = $amount;
						$recharge->status = 1;
						$recharge->save();

						$exchange = Config::get('mu.recharge.exchange');
					
						$balance = $amount;
						
						$promotion = 0;
						if($recharge_promotion > 0 && $dt >= $recharge_date[0] && $dt <= $recharge_date[1]) {
							$promotion = $balance * $recharge_promotion / 100;
						}
						
						$account = Account::where('id', $recharge->uid)->first();
					
						$account->balance = $account->balance + $balance + $promotion;
						$account->save();
						
						Helper::mobiCarDaily($account->id,$amount,$recharge->code); 
						
						
						return '1';			
						
					} else {				
						$recharge->status = 2;			
						$recharge->save();
						
						return '0';						
					}
				}					
			} 
		}
		
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
