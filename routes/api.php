<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

// endpoint untuk register dan login (tidak perlu token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// endpoint hanya bisa diakses user yang sudah login (pakai token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/me', [AuthController::class, 'me']); // ambil data user login
    Route::post('/logout', [AuthController::class, 'logout']); // logout user

    // Produk (CRUD produk milik user yang sedang login)
    Route::apiResource('/products', ProductController::class);
    // Bisa akses: index, show, store, update, destroy
    // Route::put('/products/{id}', [ProductController::class, 'update']);
});

// route khusus role admin (harus login + punya role admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Manajemen user
    Route::get('/users', [UserController::class, 'index']);             // lihat semua user
    Route::put('/users/{id}', [UserController::class, 'update']);       // update user
    Route::delete('/users/{id}', [UserController::class, 'destroy']);   // hapus user
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']); // reset password
});

// Route::get('/php-info', function () {
//     phpinfo();
// });
