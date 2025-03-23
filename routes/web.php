<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\CouponController; 
use App\Http\Controllers\Frontend\StripePaymentController;
use App\Http\Controllers\Instructor\CourseLectureController;
use App\Http\Controllers\Instructor\CourseSectionController;
use App\Http\Controllers\Admin\InstructorManagementController;
use App\Http\Controllers\Instructor\ReviewController as InstructorReviewController;

// Home Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Routes 
Route::name('')->group(function () {
    require base_path('routes/auth/web.php');

 Route::get('/dashboard', function () {
    return view('User.index');
})->middleware(['auth:web', 'verified'])->name('dashboard');

Route::middleware(['auth:web'])->group(function () {
 
   Route::post('/cart/add/{id}', [CartController::class, 'AddToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'MyCart'])->name('cart');
    Route::delete('/cart/remove/{id}', [CartController::class, 'CartRemove'])->name('cart.remove');
    Route::post('/coupon/apply', [CartController::class, 'CouponApply'])->name('coupon.apply');
    Route::post('/coupon/remove/{couponName}', [CartController::class, 'CouponRemove'])->name('coupon.remove');
    Route::get('/checkout', [CartController::class, 'CheckoutCreate'])->name('checkout.create');
    Route::post('/checkout/process', [CartController::class, 'CheckoutProcess'])->name('checkout.process');
  


    Route::resource('reviews', ReviewController::class)->names('user.reviews')->except(['create', 'store', 'show']);


    Route::resource('reviews', ReviewController::class)->names('user.reviews')->except(['create', 'store', 'show']);
});
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require base_path('routes/auth/admin.php');

    Route::get('/dashboard', function () {
        return view('admin.index');
    })->middleware(['auth:admin'])->name('dashboard');
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        // Resource Routes
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->names('courses');
        Route::resource('instructors', InstructorManagementController::class)->names('instructors');
        // Custom Routes
        Route::post('/courses/update-status', [AdminCourseController::class, 'UpdateCourseStatus'])->name('update.course.status');
        Route::post('/instructors/status', [InstructorManagementController::class, 'updateStatus'])->name('update.instructor.status');
        // Review Routes
        Route::get('/pending/review', [AdminReviewController::class, 'pending'])->name('pending.review');
        Route::get('/active/review', [AdminReviewController::class, 'active'])->name('active.review');
        Route::post('/update/review/status', [AdminReviewController::class, 'updateStatus'])->name('update.review.status');
    });
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    require base_path('routes/auth/instructor.php');

    Route::get('/dashboard', function () {
        return view('instructor.index');
    })->middleware(['auth:instructor'])->name('dashboard');
    Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::resource('courses', CourseController::class)->names('courses');
        Route::get('/courses/subcategory/ajax/{category_id}', [CourseController::class, 'getSubCategory'])->name('subcategory.ajax');
        Route::resource('courses.sections', CourseSectionController::class)->names('course_sections');
        Route::resource('courses.lectures', CourseLectureController::class)->names('course_lectures');
        Route::get('/all/review', [InstructorReviewController::class, 'index'])->name('all.review');
        // Coupon Resource Route
        Route::resource('coupon', CouponController::class)->names('coupon');
    });
});

// Frontend Routes (Public)
Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails'])->name('course.details');
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse'])->name('category.course');
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse'])->name('subcategory.course');
Route::get('/instructor/details/{id}', [IndexController::class, 'InstructorDetails'])->name('instructor.details');
Route::get('/courses', [IndexController::class, 'AllCourses'])->name('courses.all');

Route::get('/courses', [IndexController::class, 'courses'])->name('course.list');

