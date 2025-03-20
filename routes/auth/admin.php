<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\AdminRegisteredUserController;
use App\Http\Controllers\Auth\Admin\AdminEmailVerificationController;
use App\Http\Controllers\Auth\Admin\AdminPasswordResetLinkController;
use App\Http\Controllers\Auth\Admin\AdminNewPasswordController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Auth\Admin\GoogleAuthController as AdminGoogleAuthController;

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthenticatedSessionController::class, 'store']);
    Route::get('/register', [AdminRegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [AdminRegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [AdminPasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [AdminPasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [AdminNewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [AdminNewPasswordController::class, 'store'])->name('password.update');

    // Routes Google Authentication 
    Route::get('/auth/google/redirect', [AdminGoogleAuthController::class, 'redirectToGoogle'])->name('social.google.redirect');
    Route::get('/auth/google/callback', [AdminGoogleAuthController::class, 'handleGoogleCallback'])->name('social.google.callback');
});
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        // Admin Profile Routes
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        });

Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:admin')
    ->name('logout');

Route::get('/email/verify', [AdminEmailVerificationController::class, 'notice'])
    ->middleware('auth:admin')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AdminEmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
Route::post('/email/verification-notification', [AdminEmailVerificationController::class, 'resend'])
    ->middleware('auth:admin')
    ->name('verification.send');