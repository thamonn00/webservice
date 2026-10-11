<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SightingController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/pets', [PetController::class, 'index']);
Route::get('/pets/{pet}', [PetController::class, 'show']);

Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/{report}', [ReportController::class, 'show'])->whereNumber('report');
Route::get('/reports/{report}/matches', [ReportController::class, 'matches'])->whereNumber('report');
Route::get('/sightings', [SightingController::class, 'index']);
Route::get('/sightings/{sighting}', [SightingController::class, 'show']);

Route::get('/provinces', [CatalogController::class, 'provinces']);
Route::get('/provinces/{province}', [CatalogController::class, 'province']);
Route::get('/districts', [CatalogController::class, 'districts']);
Route::get('/districts/{district}', [CatalogController::class, 'district']);
Route::get('/subdistricts', [CatalogController::class, 'subdistricts']);
Route::get('/subdistricts/{subdistrict}', [CatalogController::class, 'subdistrict']);
Route::get('/species', [CatalogController::class, 'species']);
Route::get('/species/{species}', [CatalogController::class, 'speciesItem']);
Route::get('/breeds', [CatalogController::class, 'breeds']);
Route::get('/breeds/{breed}', [CatalogController::class, 'breed']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// ผู้ใช้ที่เข้าสู่ระบบเขียนข้อมูลได้ แต่แก้/ลบได้เฉพาะของตัวเอง (แอดมินแก้ได้ทุกรายการ)
// การตรวจเจ้าของอยู่ใน controller (ownedPet / ownedReport / ownedSighting)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pets', PetController::class)->only(['store', 'update', 'destroy']);

    Route::post('/reports', [ReportController::class, 'store']);
    Route::match(['put', 'patch'], '/reports/{report}', [ReportController::class, 'update']);
    Route::delete('/reports/{report}', [ReportController::class, 'destroy']);

    Route::apiResource('sightings', SightingController::class)->only(['store', 'update', 'destroy']);
});
