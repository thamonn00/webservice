<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'กรุณาเข้าสู่ระบบ'], 401);
            }

            return redirect()->guest(route('admin.login'));
        }

        if ($user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'ไม่มีสิทธิ์ดำเนินการ'], 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
