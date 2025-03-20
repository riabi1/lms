<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Instructor\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\Instructor\InstructorEmailVerificationController;
use App\Http\Controllers\Auth\Instructor\InstructorPasswordResetLinkController;
use App\Http\Controllers\Auth\Instructor\InstructorAuthenticatedSessionController;
use App\Http\Controllers\Auth\Instructor\InstructorNewPasswordController;
use App\Http\Controllers\Instructor\InstructorProfileController;
use App\Http\Controllers\Auth\Instructor\GoogleAuthController as InstructorGoogleAuthController;

Route::middleware('guest:instructor')->group(function () {
    Route::get('/login', [InstructorAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [InstructorAuthenticatedSessionController::class, 'store']);
    Route::get('/register', [InstructorRegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [InstructorRegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [InstructorPasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [InstructorPasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [InstructorNewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [InstructorNewPasswordController::class, 'store'])->name('password.update');

    // Routes Google Authentication 
    Route::get('/auth/google/redirect', [InstructorGoogleAuthController::class, 'redirectToGoogle'])->name('social.google.redirect');
    Route::get('/auth/google/callback', [InstructorGoogleAuthController::class, 'handleGoogleCallback'])->name('social.google.callback');
});

    Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [InstructorProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });


Route::post('/logout', [InstructorAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:instructor')
    ->name('logout');

Route::get('/email/verify', [InstructorEmailVerificationController::class, 'notice'])
    ->middleware('auth:instructor')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [InstructorEmailVerificationController::class, 'verify'])
    ->middleware(['auth:instructor', 'signed'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [InstructorEmailVerificationController::class, 'resend'])
    ->middleware('auth:instructor')
    ->name('verification.send');