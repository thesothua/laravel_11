<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\Api\AuthController;
use Modules\UserManagement\Http\Controllers\Api\PermissionController;
use Modules\UserManagement\Http\Controllers\Api\RoleController;
use Modules\UserManagement\Http\Controllers\UserManagementController;

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

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('usermanagement', UserManagementController::class)->names('usermanagement');

        Route::resource('permissions', PermissionController::class);
    });
    Route::resource('roles', RoleController::class);
});
