<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AdminRegisteredUserController;
use App\Http\Controllers\Auth\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\InstructorAuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// =====================
// 🔹 User Authentication
// =====================
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

// =====================
// 🔹 Email Verification (Users)
// =====================
Route::get('/email/verify', fn() => view('auth.verify-email'))
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = \App\Models\User::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return redirect()->route('login')->with('error', 'Lien de vérification invalide.');
    }
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('login')->with('message', 'Email déjà vérifié.');
    }
    $user->markEmailAsVerified();
    auth()->logout();
    return redirect()->route('login')->with('message', 'Email vérifié avec succès. Veuillez vous connecter.');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// =====================
// 🔹 Admin Authentication
// =====================
Route::get('/admin/register', [AdminRegisteredUserController::class, 'create'])->name('admin.register');
Route::post('/admin/register', [AdminRegisteredUserController::class, 'store']);
Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

Route::get('/admin/dashboard', fn() => view('admin.dashboard'))
    ->middleware(['auth:admin', 'verified'])
    ->name('admin.dashboard');

// Admin Email Verification
Route::get('/admin/email/verify', fn() => view('auth.admin-verify-email'))
    ->name('admin.verification.notice');

Route::get('/admin/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $admin = \App\Models\Admin::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($admin->getEmailForVerification()))) {
        return redirect()->route('admin.login')->with('error', 'Lien de vérification invalide.');
    }
    if ($admin->hasVerifiedEmail()) {
        return redirect()->route('admin.login')->with('message', 'Email déjà vérifié.');
    }
    $admin->markEmailAsVerified();
    auth()->guard('admin')->logout();
    return redirect()->route('admin.login')->with('message', 'Email vérifié avec succès. Veuillez vous connecter.');
})->middleware(['signed'])->name('admin.verification.verify');

Route::post('/admin/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé.');
})->middleware(['auth:admin', 'throttle:6,1'])->name('admin.verification.send');

// =====================
// 🔹 Instructor Authentication
// =====================
Route::get('/instructor/register', [InstructorRegisteredUserController::class, 'create'])->name('instructor.register');
Route::post('/instructor/register', [InstructorRegisteredUserController::class, 'store']);
Route::get('/instructor/login', [InstructorAuthenticatedSessionController::class, 'create'])->name('instructor.login');
Route::post('/instructor/login', [InstructorAuthenticatedSessionController::class, 'store']);
Route::post('/instructor/logout', [InstructorAuthenticatedSessionController::class, 'destroy'])->name('instructor.logout');

Route::get('/instructor/dashboard', fn() => view('instructor.dashboard'))
    ->middleware(['auth:instructor', 'verified'])
    ->name('instructor.dashboard');

// Instructor Email Verification
Route::get('/instructor/email/verify', fn() => view('auth.instructor-verify-email'))
    ->name('instructor.verification.notice');

Route::get('/instructor/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $instructor = \App\Models\Instructor::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($instructor->getEmailForVerification()))) {
        return redirect()->route('instructor.login')->with('error', 'Lien de vérification invalide.');
    }
    if ($instructor->hasVerifiedEmail()) {
        return redirect()->route('instructor.login')->with('message', 'Email déjà vérifié.');
    }
    $instructor->markEmailAsVerified();
    auth()->guard('instructor')->logout();
    return redirect()->route('instructor.login')->with('message', 'Email vérifié avec succès. Veuillez vous connecter.');
})->middleware(['signed'])->name('instructor.verification.verify');

Route::post('/instructor/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Un nouveau lien de vérification a été envoyé.');
})->middleware(['auth:instructor', 'throttle:6,1'])->name('instructor.verification.send');