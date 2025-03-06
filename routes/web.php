<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\InstructorRegisteredUserController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\InstructorAuthenticatedSessionController;
use App\Http\Controllers\Auth\AdminRegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->middleware('auth:admin')->name('admin.dashboard');

// Admin Registration (restricted to authenticated admins)
Route::get('/admin/register', [AdminRegisteredUserController::class, 'create'])->middleware('ensure.is.admin')->name('admin.register');
Route::post('/admin/register', [AdminRegisteredUserController::class, 'store'])->middleware('ensure.is.admin');



// Instructor Routes
Route::get('/instructor/login', [InstructorAuthenticatedSessionController::class, 'create'])->name('instructor.login');
Route::post('/instructor/login', [InstructorAuthenticatedSessionController::class, 'store']);
Route::post('/instructor/logout', [InstructorAuthenticatedSessionController::class, 'destroy'])->name('instructor.logout');
Route::get('/instructor/dashboard', fn() => view('instructor.dashboard'))->middleware('auth:instructor')->name('instructor.dashboard');

Route::get('/instructor/register', [InstructorRegisteredUserController::class, 'create'])->middleware('ensure.is.admin')->name('instructor.register');
Route::post('/instructor/register', [InstructorRegisteredUserController::class, 'store'])->middleware('ensure.is.admin');

require __DIR__.'/auth.php';
