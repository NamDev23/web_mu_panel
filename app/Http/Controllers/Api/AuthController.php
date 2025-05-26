<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Account;
use App\Helper;

use Carbon\Carbon;
use Validator;
use Auth;
use Hash;
use Config;
use Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /*Add coin*/
    public function AddCoin(Request $request)
    {
        $account = Auth::user();
        if (!$account) {
            $username = trim($request->username);
            echo $username;
            if ($request->username) {
                $account = Account::where('username', $username)->first();
            } else {
                return Response::json(['message' => 'You need login!', 'status' => 0]);
            }
        }
        $amount = $request->amount;
        if (!$amount) {
            return Response::json(['message' => 'is a number!', 'status' => 0]);
        }
        $account->Money = $account->Money + $amount;
        $update = $account->save();
        return Response::json(['message' => "add $amount coin.", 'status' => 1]);
    }


    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|unique:account.zt_account,username|alpha_num|between:5,20',
            'password' => 'required|alpha_num|between:4,32',
            'email' => 'nullable|email|unique:account.zt_account|max:255',
        ]);

        $ip = $request->ip();

        $code = 1;
        $username = NULL;
        $token = NULL;

        if ($validator->fails()) {

            $msg = $validator->errors()->first();
        } else {

            try {

                $username = Helper::str_lower($request->user_name);
                $token = Str::random(8);
                $platform_name = Config::get('mu.platform.name') ? Config::get('mu.platform.name') : [];
                $userid = $platform_name . $token;

                $lang = Helper::getLangCode();

                if ($request->has('device') && $request->device == 'ios10') {
                    $ip = $token;
                } else {

                    //$password = md5($request->password);
                }

                $password = $request->password;

                // $account = Account::create([
                // 'username' => $username,
                // 'password' => $request->password,
                // 'email' => $request->email,
                // 'ip' => $request->ip(),
                // 'userid' => $userid,
                // 'hash' => Hash::make(md5($password)),
                // ]);

                $account = [
                    'UserName' => $username,
                    'Password' => md5($password),
                    'Email' => $request->email,
                    'IPAddress' => $request->ip(),
                    'UserID' => $userid,
                ];
                // dd($account);
                $account = Account::create($account);

                $code = 0;
                $msg = __('usercp.message_register_success');
            } catch (\Exception $e) {
                $msg = __('usercp.message_register_failed');
            }
        }

        $data = Helper::create_api($code, $msg, $username, $ip, 1, $username, $token, Str::random(32));

        return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:account.zt_account|between:4,20',
            'userpass' => 'required|alpha_num|between:4,32',
        ]);

        $ip = $request->ip();
        $ipv4 = Config::get('ipban.ip');

        if ($request->has('device') && $request->device == 'ios10us') {
            $password = $request->userpass;
        } else {
            //$password = md5($request->userpass);
        }

        $password = $request->userpass;

        $code = 1;
        $username = NULL;
        $token = NULL;

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
        } elseif (in_array($ip, $ipv4)) {

            $msg = __('usercp.message_ip_banned');
        } else {
            $checkUser = Account::where(['username' => Helper::str_lower($request->username), 'password' => $password])->first();
            if ($checkUser) {
                Auth::login($checkUser);


                $user = Auth::user();

                if ($user->groupid == 9) {
                    $msg = __('usercp.message_login_banned');
                } else {

                    $code = 0;
                    $msg = __('usercp.message_login_success');

                    $username = Helper::str_lower($request->username);

                    $userid = $user->userid;

                    $platform_name = Config::get('mu.platform.name');

                    $token = substr($userid, strlen($platform_name));

                    if ($request->has('device') && $request->device == 'ios10us') {
                        $ip = $token;
                    }
                }
            } else {
                $msg = __('usercp.message_login_failed');
            }
        }

        $data = Helper::create_api($code, $msg, $username, $ip, 1, $username, $token, Str::random(32));

        return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function init()
    {
        $data = [
            'retcode' => 0,
            'retmsg' => 'success',
            'data' => []
        ];

        return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getOrder()
    {

        $data = [
            'retcode' => '88888',
            'retmsg' => 'success'
        ];

        return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
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
    public function ios_login(Request $request)
    {

        $user = Auth::user();
        $ip = $request->ip();

        $platform_name = Config::get('mu.platform.name');
        $token =  substr($user->userid, strlen($platform_name));

        $data  = [
            "retcode" => 0,
            "retmsg" => __("usercp.message_login_success"),
            "data" => [
                "uid" => $user->username,
                "indulge" => 1,
                "ipv4" => $token,
                "uname"  => $user->username,
                "KL_SSO" => $token,
                "KL_PERSON" => Str::random(32)
            ],
            "isnew" => "true"
        ];

        return Response::json($data, 200, [], JSON_UNESCAPED_UNICODE);
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
