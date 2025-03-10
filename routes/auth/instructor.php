<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Instructor\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\Instructor\InstructorEmailVerificationController;
use App\Http\Controllers\Auth\Instructor\InstructorPasswordResetLinkController;
use App\Http\Controllers\Auth\Instructor\InstructorAuthenticatedSessionController;
use App\Http\Controllers\Auth\Instructor\InstructorNewPasswordController;

Route::middleware('guest:instructor')->group(function () {
  Route::get('/login', [InstructorAuthenticatedSessionController::class, 'create'])->name('login');
  Route::post('/login', [InstructorAuthenticatedSessionController::class, 'store']);
  Route::get('/register', [InstructorRegisteredUserController::class, 'create'])->name('register');
  Route::post('/register', [InstructorRegisteredUserController::class, 'store']);
  Route::get('/forgot-password', [InstructorPasswordResetLinkController::class, 'create'])->name('password.request');
  Route::post('/forgot-password', [InstructorPasswordResetLinkController::class, 'store'])->name('password.email');
  Route::get('/reset-password/{token}', [InstructorNewPasswordController::class, 'create'])->name('password.reset');
  Route::post('/reset-password', [InstructorNewPasswordController::class, 'store'])->name('password.update');
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
