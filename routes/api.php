
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ---------- 1.2 register/login (สาธารณะ ไม่ต้องมี token) ----------
// รับจาก: Thunder Client POST /api/register , POST /api/login (Body JSON)
// ส่งไป: AuthController@register / @login -> สร้าง User ในตาราง users -> สร้าง token ตาราง personal_access_tokens
// ส่งกลับ: {status,message,data:user,token} เอา token ไปใช้เส้นอื่นต่อ
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// ---------- ทุกเส้นด้านล่างต้องมี token (Thunder Client) ----------
// Header ทุกครั้ง: Accept: application/json + Authorization: Bearer <token>
// ที่มา token: copy จาก response ของ /register,/login หรือกดสร้างที่หน้าเว็บ /pets -> POST /api-token
// กระแส: Header -> Sanctum ถอดรหัส token -> $request->user() คือ User ที่ login อยู่
Route::middleware('auth:sanctum')->group(function () {
    // 1.2 logout/me
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // 1.3 getAll + getByID (ต้องมี token เหมือนตัวอย่างที่เรียน)
    Route::get('/pet', [PetController::class, 'index']);
    Route::get('/pet/{pet}', [PetController::class, 'show']);

    // ชนิดสัตว์ / จังหวัด / อำเภอ / ตำบล (ต้องมี token)
    Route::get('/species', [CatalogController::class, 'species']);
    Route::get('/species/{species}', [CatalogController::class, 'speciesShow']);
    Route::get('/breeds', [CatalogController::class, 'breeds']);
    Route::get('/breeds/{breed}', [CatalogController::class, 'breedShow']);
    Route::get('/provinces', [CatalogController::class, 'provinces']);
    Route::get('/provinces/{province}', [CatalogController::class, 'provinceShow']);
    Route::get('/districts', [CatalogController::class, 'districts']);
    Route::get('/districts/{district}', [CatalogController::class, 'districtShow']);
    Route::get('/subdistricts', [CatalogController::class, 'subdistricts']);
    Route::get('/subdistricts/{subdistrict}', [CatalogController::class, 'subdistrictShow']);

    // ---------- 1.4 insert / update / delete (ต้องมี token + เจ้าของหรือ admin) ----------
    Route::post('/pet', [PetController::class, 'store']);
    Route::match(['put', 'patch'], '/pet/{pet}', [PetController::class, 'update']);
    Route::delete('/pet/{pet}', [PetController::class, 'destroy']);

    // ---------- ประกาศหาย/พบ (อ่าน+เพิ่มทุกคน, แก้/ลบเฉพาะเจ้าของหรือ admin) ----------
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::match(['put', 'patch'], '/reports/{report}', [ReportController::class, 'update']);
    Route::delete('/reports/{report}', [ReportController::class, 'destroy']);

    // ---------- เฉพาะแอดมิน: จัดการ users + เพิ่ม/แก้/ลบ species,breeds ----------
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        Route::post('/species', [CatalogController::class, 'storeSpecies']);
        Route::match(['put', 'patch'], '/species/{species}', [CatalogController::class, 'updateSpecies']);
        Route::delete('/species/{species}', [CatalogController::class, 'destroySpecies']);
        Route::post('/breeds', [CatalogController::class, 'storeBreed']);
        Route::match(['put', 'patch'], '/breeds/{breed}', [CatalogController::class, 'updateBreed']);
        Route::delete('/breeds/{breed}', [CatalogController::class, 'destroyBreed']);
    });
});
