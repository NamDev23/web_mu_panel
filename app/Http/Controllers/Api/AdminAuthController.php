<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Account;

class AdminAuthController extends Controller
{
    /**
     * Admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Find user by username
            $user = Account::where('UserName', $request->username)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản không tồn tại'
                ], 401);
            }

            // Check password (MD5 hash)
            if (md5($request->password) !== $user->Password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mật khẩu không chính xác'
                ], 401);
            }

            // Check if user is admin
            if (!in_array($user->groupid, [1, 2])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập admin panel'
                ], 403);
            }

            // Create token
            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'user' => [
                    'UserID' => $user->UserID,
                    'UserName' => $user->UserName,
                    'Email' => $user->Email,
                    'groupid' => $user->groupid,
                    'Money' => $user->Money,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng xuất'
            ], 500);
        }
    }

    /**
     * Get current admin user
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'UserID' => $user->UserID,
                    'UserName' => $user->UserName,
                    'Email' => $user->Email,
                    'groupid' => $user->groupid,
                    'Money' => $user->Money,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin user'
            ], 500);
        }
    }
}
