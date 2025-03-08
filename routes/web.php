<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\VerificationStatusController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Admin\AdminRegisteredUserController;
use App\Http\Controllers\Auth\Admin\AdminEmailVerificationController;
use App\Http\Controllers\Auth\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\Instructor\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\Instructor\InstructorEmailVerificationController;
use App\Http\Controllers\Auth\Instructor\InstructorAuthenticatedSessionController;

Route::get('/', function () {
  return view('welcome');
});

// User Authentication
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/dashboard', fn() => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Email Verification (Users)
Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
  ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
  ->middleware(['signed'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
  ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();
  return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Admin Authentication
Route::get('/admin/register', [AdminRegisteredUserController::class, 'create'])->name('admin.register');
Route::post('/admin/register', [AdminRegisteredUserController::class, 'store']);
Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

Route::get('/admin/dashboard', fn() => view('admin.dashboard'))
  ->middleware(['auth:admin', 'verified'])
  ->name('admin.dashboard');

// Admin Email Verification
Route::get('/admin/email/verify', [AdminEmailVerificationController::class, 'notice'])
  ->name('admin.verification.notice');
Route::get('/admin/email/verify/{id}/{hash}', [AdminEmailVerificationController::class, 'verify'])
  ->middleware(['signed'])->name('admin.verification.verify');
Route::post('/admin/email/verification-notification', [AdminEmailVerificationController::class, 'resend'])
  ->middleware(['auth:admin', 'throttle:6,1'])->name('admin.verification.send');

// Instructor Authentication
Route::get('/instructor/register', [InstructorRegisteredUserController::class, 'create'])->name('instructor.register');
Route::post('/instructor/register', [InstructorRegisteredUserController::class, 'store']);
Route::get('/instructor/login', [InstructorAuthenticatedSessionController::class, 'create'])->name('instructor.login');
Route::post('/instructor/login', [InstructorAuthenticatedSessionController::class, 'store']);
Route::post('/instructor/logout', [InstructorAuthenticatedSessionController::class, 'destroy'])->name('instructor.logout');

Route::get('/instructor/dashboard', fn() => view('instructor.dashboard'))
  ->middleware(['auth:instructor', 'verified'])
  ->name('instructor.dashboard');

// Instructor Email Verification
Route::get('/instructor/email/verify', [InstructorEmailVerificationController::class, 'notice'])
  ->name('instructor.verification.notice');
Route::get('/instructor/email/verify/{id}/{hash}', [InstructorEmailVerificationController::class, 'verify'])
  ->middleware(['signed'])->name('instructor.verification.verify');
Route::post('/instructor/email/verification-notification', [InstructorEmailVerificationController::class, 'resend'])
  ->middleware(['auth:instructor', 'throttle:6,1'])->name('instructor.verification.send');

Route::get('/check-verification', [VerificationStatusController::class, 'check'])
  ->middleware('auth:web,admin,instructor')->name('verification.check');