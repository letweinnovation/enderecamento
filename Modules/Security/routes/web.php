<?php

use Illuminate\Support\Facades\Route;
use Modules\Security\Http\Controllers\AuthController;

Route::middleware('web')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/auth/dev-login', [AuthController::class, 'devLogin'])->name('auth.dev-login');
    Route::get('/auth/callback', [AuthController::class, 'handleCallback'])->name('auth.callback');

    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
