<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Instructor\CourseLectureController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Student\ReviewController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Instructor\CourseSectionController;
use App\Http\Controllers\Admin\InstructorManagementController;
use App\Http\Controllers\Instructor\InstructorProfileController;

// Home Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Routes (web guard)
Route::name('')->group(function () {
    // Authentication Routes for Users Only 
 require base_path('routes/auth/web.php');
    // Dashboard for authenticated users
    Route::get('/dashboard', function () {
        return view('frontend.dashboard.index');
    })->middleware(['auth:web', 'verified'])->name('dashboard');

    // Profile Routes
    Route::middleware(['auth:web', 'verified'])->group(function () {
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('/store/review', [ReviewController::class, 'StoreReview'])->name('store.review');
    });

    // User Review Routes
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/my/reviews', [ReviewController::class, 'UserReviews'])->name('user.reviews');
        Route::get('/review/edit/{id}', [ReviewController::class, 'UserReviewEdit'])->name('user.review.edit');
        Route::put('/review/update/{id}', [ReviewController::class, 'UserReviewUpdate'])->name('user.review.update');
        Route::delete('/review/delete/{id}', [ReviewController::class, 'UserReviewDelete'])->name('user.review.delete');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    require base_path('routes/auth/admin.php');

    // Admin Dashboard
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->middleware(['auth:admin', 'verified'])->name('dashboard');

    // Admin Profile Routes
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });

    // Admin Resource and Custom Routes
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);

        // Instructor Management
        Route::get('/instructors', [InstructorManagementController::class, 'index'])->name('instructors.index');
        Route::post('/instructors/status', [InstructorManagementController::class, 'updateStatus'])->name('update.instructor.status');

        // Admin Course Routes
        Route::controller(AdminCourseController::class)->group(function () {
            Route::get('/all/course', 'AdminAllCourse')->name('all.course');
            Route::post('/update/course/status', 'UpdateCourseStatus')->name('update.course.status');
            Route::get('/course/details/{id}', 'AdminCourseDetails')->name('course.details');
        });

        // Reviews
        Route::get('/pending/review', [ReviewController::class, 'AdminPendingReview'])->name('pending.review');
        Route::get('/active/review', [ReviewController::class, 'AdminActiveReview'])->name('active.review');
        Route::post('/update/review/status', [ReviewController::class, 'UpdateReviewStatus'])->name('update.review.status');
    });
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    // Instructor Authentication Routes
    require base_path('routes/auth/instructor.php');

    // Instructor Dashboard
    Route::get('/dashboard', function () {
        return view('instructor.index');
    })->middleware(['auth:instructor', 'verified'])->name('dashboard');

    // Instructor Profile Routes
    Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [InstructorProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    });

    // Instructor Course Routes
    Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::resource('courses', CourseController::class)->names('courses');
        Route::get('/courses/subcategory/ajax/{category_id}', [CourseController::class, 'getSubCategory'])->name('subcategory.ajax');
        Route::resource('courses.sections', CourseSectionController::class)->names('course_sections');
        Route::resource('courses.lectures', CourseLectureController::class)->names('course_lectures');
        Route::get('/all/review', [ReviewController::class, 'InstructorAllReview'])->name('all.review');
    });
});

// Frontend Routes (Public)
Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails'])->name('course.details');
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse'])->name('category.course');
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse'])->name('subcategory.course');