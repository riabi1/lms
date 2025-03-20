<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\User\RegisteredUserController;
use App\Http\Controllers\Auth\User\EmailVerificationController;
use App\Http\Controllers\Auth\User\PasswordResetLinkController;
use App\Http\Controllers\Auth\User\AuthenticatedSessionController;
use App\Http\Controllers\Auth\User\NewPasswordController;
use App\Http\Controllers\Auth\User\GoogleAuthController;
use App\Http\Controllers\User\ProfileController; 
use App\Http\Controllers\User\ReviewController; 

Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    // Routes Google Authentication
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('social.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('social.google.callback');
});

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/store/review', [ReviewController::class, 'store'])->name('store.review');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:web')
    ->name('logout');

Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
    ->middleware('auth:web')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['auth:web', 'signed'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->middleware('auth:web')
    ->name('verification.send');