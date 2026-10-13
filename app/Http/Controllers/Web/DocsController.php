<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    public function index()
    {
        $pet = Pet::with(['species', 'breed', 'photos', 'user:id,name'])->latest('id')->first();

        return view('apidocs', [
            'petJson' => $pet ? $pet->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}',
        ]);
    }

    public function createToken(Request $request)
    {
        $token = $request->user()->createToken('Thunder Client')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'สร้าง Token สำเร็จ คัดลอกเก็บไว้ เพราะจะแสดงให้เห็นครั้งเดียว',
            'data' => null,
            'token' => $token,
        ], 201);
    }
}
