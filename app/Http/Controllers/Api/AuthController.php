<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /** POST /api/register — สมัครสมาชิก ได้ token กลับทันที */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'ข้อมูลที่ส่งมาไม่ถูกต้อง',
                'data' => null,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'สมัครสมาชิกสำเร็จ',
            'data' => $user,
            'token' => $user->createToken('authToken')->plainTextToken,
        ], 201);
    }

    /** POST /api/login — ได้ token กลับมา */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
                'data' => null,
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'เข้าสู่ระบบสำเร็จ',
            'data' => $user,
            'token' => $user->createToken('authToken')->plainTextToken,
        ]);
    }

    /** POST /api/logout — ลบ token ปัจจุบัน */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'ออกจากระบบสำเร็จ',
            'data' => null,
        ]);
    }

    /** GET /api/me — ข้อมูลตัวเองจาก token */
    public function me(Request $request)
    {
        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $request->user()]);
    }
}
