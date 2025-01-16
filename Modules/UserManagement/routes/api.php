<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\Api\AuthController;
use Modules\UserManagement\Http\Controllers\Api\CustomerController;
use Modules\UserManagement\Http\Controllers\Api\PermissionController;
use Modules\UserManagement\Http\Controllers\Api\RoleController;
use Modules\UserManagement\Http\Controllers\Api\UserController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
 */

Route::prefix('usermanagement')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('usermanagement.register');
    Route::post('/login', [AuthController::class, 'login'])->name('usermanagement.login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('usermanagement.logout');
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('usermanagement.password.email');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('usermanagement.password.reset');

    
    Route::post('email/resend', [AuthController::class, 'resend'])->name('verification.resend');
    Route::get('email/notice', [AuthController::class, 'notice'])->name('verification.notice');

    Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
        // Route::get('/users', [UserController::class, 'index']);
        // Route::get('/users', [UserController::class, 'index']);
        // Route::get('/users', [UserController::class, 'index']);
        // Route::post('/users', [UserController::class, 'store']);
        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('customer', CustomerController::class);
    });

});
