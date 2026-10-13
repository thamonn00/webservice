<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** กัน route เฉพาะแอดมิน: role !== admin -> 403 (ไฟล์ใหม่ไฟล์เดียว ใช้ทั้ง API+เว็บ) */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user() ?? auth()->user();

        if (! $user || ($user->role ?? null) !== 'admin') {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'ไม่มีสิทธิ์ดำเนินการ (เฉพาะแอดมิน)',
                    'data' => null,
                ], 403);
            }
            abort(403, 'ไม่มีสิทธิ์ดำเนินการ (เฉพาะแอดมิน)');
        }

        return $next($request);
    }
}
