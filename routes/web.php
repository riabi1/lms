<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\AdminRegisteredUserController;
use App\Http\Controllers\Auth\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\Instructor\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\Instructor\InstructorAuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\Admin\AdminEmailVerificationController;
use App\Http\Controllers\Auth\Instructor\InstructorEmailVerificationController;
use App\Http\Controllers\Auth\VerificationStatusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Instructor\ProfileController as InstructorProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

// User Authentication
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Email Verification
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
    ->name('verification.send');

// Admin Authentication
Route::get('/admin/register', [AdminRegisteredUserController::class, 'create'])->name('admin.register');
Route::post('/admin/register', [AdminRegisteredUserController::class, 'store']);
Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

// Admin Profile Routes
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::patch('/admin/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.update.password');
    Route::delete('/admin/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

// Instructor Authentication
Route::get('/instructor/register', [InstructorRegisteredUserController::class, 'create'])->name('instructor.register');
Route::post('/instructor/register', [InstructorRegisteredUserController::class, 'store']);
Route::get('/instructor/login', [InstructorAuthenticatedSessionController::class, 'create'])->name('instructor.login');
Route::post('/instructor/login', [InstructorAuthenticatedSessionController::class, 'store']);
Route::post('/instructor/logout', [InstructorAuthenticatedSessionController::class, 'destroy'])->name('instructor.logout');
Route::get('/instructor/dashboard', fn() => view('instructor.dashboard'))->name('instructor.dashboard');

// Instructor Profile Routes
Route::middleware('auth:instructor')->group(function () {
    Route::get('/instructor/edit', [InstructorProfileController::class, 'edit'])->name('instructor.profile.edit');
    Route::patch('/instructor/profile', [InstructorProfileController::class, 'update'])->name('instructor.profile.update');
    Route::patch('/instructor/profile/password', [InstructorProfileController::class, 'updatePassword'])->name('instructor.profile.update.password');
    Route::delete('/instructor/profile', [InstructorProfileController::class, 'destroy'])->name('instructor.profile.destroy');
});

// Instructor Email Verification
Route::get('/instructor/email/verify', [InstructorEmailVerificationController::class, 'notice'])->name('instructor.verification.notice');
Route::get('/instructor/email/verify/{id}/{hash}', [InstructorEmailVerificationController::class, 'verify'])
    ->middleware('signed')->name('instructor.verification.verify');
Route::post('/instructor/email/verification-notification', [InstructorEmailVerificationController::class, 'resend'])
    ->name('instructor.verification.send');

// Verification Status Check
Route::get('/check-verification', [VerificationStatusController::class, 'check'])->name('verification.check');