<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerificationStatusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Instructor\InstructorProfileController;
use App\Http\Controllers\Auth\Admin\AdminRegisteredUserController;
use App\Http\Controllers\Auth\Admin\AdminEmailVerificationController;
use App\Http\Controllers\Auth\Admin\AdminPasswordResetLinkController;
use App\Http\Controllers\Auth\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\Instructor\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\Instructor\InstructorEmailVerificationController;
use App\Http\Controllers\Auth\Instructor\InstructorPasswordResetLinkController;
use App\Http\Controllers\Auth\Instructor\InstructorAuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\AdminNewPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\Instructor\InstructorNewPasswordController;


// Welcome Route
// Route::get('/', fn() => view('welcome'))->name('welcome');

// Home Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Routes
// Authentication
Route::middleware('guest:web')->group(function () {
  Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
  Route::post('/login', [AuthenticatedSessionController::class, 'store']);
  Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
  Route::post('/register', [RegisteredUserController::class, 'store']);
  Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
  Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
  ->middleware('auth:web')
  ->name('logout');

// Dashboard
Route::get('/dashboard', fn() => view('frontend.dashboard.index'))
  ->middleware(['auth:web', 'verified'])
  ->name('dashboard');

// Profile
Route::middleware(['auth:web', 'verified'])->group(function () {
  Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});

// Email Verification
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
  ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
  ->middleware('signed')
  ->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
  ->middleware(['auth:web']) // Remove throttle in local dev
  ->name('verification.send');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
  // Authentication
  Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthenticatedSessionController::class, 'store']);
    Route::get('/register', [AdminRegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [AdminRegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [AdminPasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [AdminPasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [AdminNewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [AdminNewPasswordController::class, 'store'])->name('password.update');
  });
  Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:admin')
    ->name('logout');

  // Dashboard
  Route::get('/dashboard', fn() => view('admin.admin_dashboard'))
    ->middleware(['auth:admin', 'verified'])
    ->name('dashboard');

  Route::middleware(['auth:admin', 'verified'])->group(function () {
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');
  });
  
  // Email Verification
  Route::get('/email/verify', [AdminEmailVerificationController::class, 'notice'])
    ->name('verification.notice');
  Route::get('/email/verify/{id}/{hash}', [AdminEmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
  Route::post('/email/verification-notification', [AdminEmailVerificationController::class, 'resend'])
    ->middleware(['auth:admin']) // Remove throttle in local dev
    ->name('verification.send');
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
  // Authentication
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

  // Dashboard
  Route::get('/dashboard', fn() => view('instructor.instructor_dashboard'))
    ->middleware(['auth:instructor', 'verified'])
    ->name('dashboard');

  Route::middleware(['auth:instructor', 'verified'])->group(function () {
    Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [InstructorProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.updatePassword');
  });

  // Email Verification
  Route::get('/email/verify', [InstructorEmailVerificationController::class, 'notice'])
    ->name('verification.notice');
  Route::get('/email/verify/{id}/{hash}', [InstructorEmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
  Route::post('/email/verification-notification', [InstructorEmailVerificationController::class, 'resend'])
    ->middleware(['auth:instructor']) // Remove throttle in local dev
    ->name('verification.send');
});

// Verification Status Check (Shared across all guards)
Route::get('/check-verification', [VerificationStatusController::class, 'check'])
  ->middleware('auth:web,admin,instructor')
  ->name('verification.check');
