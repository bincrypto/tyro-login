<?php

use HasinHayder\TyroLogin\Http\Controllers\LoginController;
use HasinHayder\TyroLogin\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tyro Login Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication for the Tyro Login package.
|
*/

// Guest routes
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get(config('tyro-login.routes.login', 'login'), [LoginController::class, 'showLoginForm'])
        ->name('login');
    
    Route::post(config('tyro-login.routes.login', 'login'), [LoginController::class, 'login'])
        ->name('login.submit');

    // Registration routes
    if (config('tyro-login.registration.enabled', true)) {
        Route::get(config('tyro-login.routes.register', 'register'), [RegisterController::class, 'showRegistrationForm'])
            ->name('register');
        
        Route::post(config('tyro-login.routes.register', 'register'), [RegisterController::class, 'register'])
            ->name('register.submit');
    }
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post(config('tyro-login.routes.logout', 'logout'), [LoginController::class, 'logout'])
        ->name('logout');
});
