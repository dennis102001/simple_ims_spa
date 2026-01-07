<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::controller(RegisteredUserController::class)->group(function(){
    Route::post('/register', 'store')
        ->middleware('guest')
        ->name('register');

    Route::post('/add-user', 'addUser')
        ->middleware('auth:sanctum', 'isAdmin')
        ->name('add.user');

    Route::put('/update-user/{id}', 'updateUser')
        ->middleware('auth:sanctum', 'isAdmin')
        ->name('update.user');

    Route::delete('/delete-user/{id}', 'deleteUser')
        ->middleware('auth:sanctum', 'isAdmin')
        ->name('delete.user');

    Route::put('/update-account-info', 'updateAccountInfo')
        ->middleware('auth:sanctum');
    
    Route::put('/update-account-pass', 'updateAccountPass')
        ->middleware('auth:sanctum');
});


Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
