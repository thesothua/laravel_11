<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::prefix('usermanagement')->group(function () {
Route::get('/reset-password/{token}', function ($token) {

    dd($token);
    return view('usermanagement::emails.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
// });
