<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CRUD users เฉพาะแอดมิน (route ครอบด้วย middleware admin แล้ว) */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query()
            ->when($request->filled('q'), function ($x) use ($request) {
                $t = '%'.addcslashes($request->q, '%_\\').'%';
                $x->where(fn ($i) => $i->where('name', 'like', $t)->orWhere('email', 'like', $t));
            })
            ->when($request->filled('role'), fn ($x) => $x->where('role', $request->role))
            ->latest('id')
            ->paginate($request->input('per_page', 15));

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $q]);
    }

    public function show(User $user)
    {
        $user->loadCount('pets');

        return response()->json(['status' => true, 'message' => 'ดึงข้อมูลสำเร็จ', 'data' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'role' => ['sometimes', Rule::in(['user', 'admin'])],
        ]);

        // ห้ามแก้ role ตัวเอง กันล็อกตัวเองออกจากแอดมิน
        if (array_key_exists('role', $data) && (int) $user->id === (int) $request->user()->id) {
            return response()->json([
                'status' => false, 'message' => 'ไม่สามารถเปลี่ยน role ของตัวเองได้', 'data' => null,
            ], 403);
        }

        $user->update($data);

        return response()->json(['status' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว', 'data' => $user->refresh()]);
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) $user->id === (int) $request->user()->id) {
            return response()->json([
                'status' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้', 'data' => null,
            ], 403);
        }

        $user->delete(); // pets/reports ของ user ถูกลบต่อแบบ cascade ตาม migration

        return response()->json(['status' => true, 'message' => 'ลบข้อมูลเรียบร้อยแล้ว', 'data' => null]);
    }
}
