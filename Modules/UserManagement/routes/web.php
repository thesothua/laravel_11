<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
    // Route::resource('usermanagement', UserManagementController::class)->names('usermanagement');
// });

// Route::prefix('usermanagement')->group(function () {
    Route::get('/reset-password/{token}', function ($token) {
        return view('usermanagement::emails.reset-password', ['token' => $token]);
    })
    ->middleware('guest')->name('password.reset');
    // });


