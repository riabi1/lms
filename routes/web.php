<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\ProfileController;
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
use App\Http\Controllers\Auth\Admin\GoogleAuthController as AdminGoogleAuthController;
use App\Http\Controllers\Auth\User\GoogleAuthController;
use App\Http\Controllers\Auth\Instructor\GoogleAuthController as InstructorGoogleAuthController;

// Home Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Routes (web guard)
Route::name('')->group(function () {
    require base_path('routes/auth/web.php');

    Route::middleware('guest:web')->group(function () {
        Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])->name('social.google.redirect');
        Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('social.google.callback');
    });

    Route::get('/dashboard', function () {
        return view('frontend.dashboard.index');
    })->middleware(['auth:web', 'verified'])->name('dashboard');

    Route::middleware(['auth:web', 'verified'])->group(function () {
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::post('/store/review', [ReviewController::class, 'StoreReview'])->name('store.review');
    });

    Route::middleware(['auth:web'])->group(function () {
        Route::get('/my/reviews', [ReviewController::class, 'UserReviews'])->name('user.reviews');
        Route::get('/review/edit/{id}', [ReviewController::class, 'UserReviewEdit'])->name('user.review.edit');
        Route::put('/review/update/{id}', [ReviewController::class, 'UserReviewUpdate'])->name('user.review.update');
        Route::delete('/review/delete/{id}', [ReviewController::class, 'UserReviewDelete'])->name('user.review.delete');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require base_path('routes/auth/admin.php');

    Route::middleware('guest:admin')->group(function () {
        Route::get('/auth/google/redirect', [AdminGoogleAuthController::class, 'redirectToGoogle'])->name('social.google.redirect');
        Route::get('/auth/google/callback', [AdminGoogleAuthController::class, 'handleGoogleCallback'])->name('social.google.callback');
    });

    Route::get('/dashboard', function () {
        return view('admin.index');
    })->middleware(['auth:admin'])->name('dashboard');

    Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);

        Route::get('/instructors', [InstructorManagementController::class, 'index'])->name('instructors.index');
        Route::post('/instructors/status', [InstructorManagementController::class, 'updateStatus'])->name('update.instructor.status');

        Route::controller(AdminCourseController::class)->group(function () {
            Route::get('/all/course', 'AdminAllCourse')->name('all.course');
            Route::post('/update/course/status', 'UpdateCourseStatus')->name('update.course.status');
            Route::get('/course/details/{id}', 'AdminCourseDetails')->name('course.details');
        });

        Route::get('/pending/review', [ReviewController::class, 'AdminPendingReview'])->name('pending.review');
        Route::get('/active/review', [ReviewController::class, 'AdminActiveReview'])->name('active.review');
        Route::post('/update/review/status', [ReviewController::class, 'UpdateReviewStatus'])->name('update.review.status');
    });
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    require base_path('routes/auth/instructor.php');

    // Routes Google pour instructors
    Route::middleware('guest:instructor')->group(function () {
        Route::get('/auth/google/redirect', [InstructorGoogleAuthController::class, 'redirectToGoogle'])->name('social.google.redirect');
        Route::get('/auth/google/callback', [InstructorGoogleAuthController::class, 'handleGoogleCallback'])->name('social.google.callback');
    });

    Route::get('/dashboard', function () {
        return view('instructor.index');
    })->middleware(['auth:instructor'])->name('dashboard');

    Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [InstructorProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.updatePassword');

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