<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Instructor\InstructorProfileController;

// Home Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Routes (web guard)
Route::name('')->group(function () {
    // Include authentication routes
    require base_path('routes/auth/web.php');

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
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require base_path('routes/auth/admin.php');
    Route::get('/dashboard', fn() => view('admin.admin_dashboard'))
        ->middleware(['auth:admin', 'verified'])
        ->name('dashboard');
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });
});
// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    // Include authentication routes
    require base_path('routes/auth/instructor.php');

    // Dashboard
    Route::get('/dashboard', fn() => view('instructor.instructor_dashboard'))
        ->middleware(['auth:instructor', 'verified'])
        ->name('dashboard');

    // Profile
    Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [InstructorProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });
});