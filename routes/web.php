<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DocsController;
use App\Http\Controllers\Web\PetWebController;
use Illuminate\Support\Facades\Route;

// ---------- เว็บมินิมอล: login / สมัคร / แสดงข้อมูล / ก๊อป API / user / logout ----------
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'pets.index' : 'login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/api-token', [DocsController::class, 'createToken'])
        ->middleware('throttle:5,1')
        ->name('docs.token');
    Route::get('/pets', [PetWebController::class, 'index'])->name('pets.index');
    Route::get('/pets/{pet}', [PetWebController::class, 'show'])->name('pets.show');

    // ---------- หน้าเว็บแอดมิน (เฉพาะ role=admin) ----------
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [PetWebController::class, 'adminUsers'])->name('admin.users.index');
        Route::get('/admin/users/{user}/edit', [PetWebController::class, 'adminUsersEdit'])->name('admin.users.edit');
        Route::patch('/admin/users/{user}', [PetWebController::class, 'adminUsersUpdate'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [PetWebController::class, 'adminUsersDestroy'])->name('admin.users.destroy');
    });
});

// ---------- หน้าเอกสาร API: เปิดอ่านได้โดยไม่ต้อง login (ก๊อป URL ไปวางใน Thunder Client) ----------
Route::get('/api-docs', [DocsController::class, 'index'])->name('docs');
