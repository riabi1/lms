<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\NotesController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\QuizzesController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\User\MyCourseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Admin\AllOrdersController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\OrderController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\CouponController; 
use App\Http\Controllers\Frontend\StripePaymentController;
use App\Http\Controllers\Instructor\NotificationController;
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
 // cart rouutes
   Route::post('/cart/add/{id}', [CartController::class, 'AddToCart'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'MyCart'])->name('cart');
   Route::get('/cart/remove/{id}', [CartController::class, 'CartRemove'])->name('cart.remove');
   //coupon routes
    Route::post('/coupon/apply', [CartController::class, 'CouponApply'])->name('coupon.apply');
    Route::post('/coupon/remove/{couponName}', [CartController::class, 'CouponRemove'])->name('coupon.remove');
    // payment routes
    Route::get('/checkout', [CartController::class, 'CheckoutCreate'])->name('checkout.create');
    Route::post('/checkout/process', [CartController::class, 'CheckoutProcess'])->name('checkout.process');
  //revious routes
    Route::resource('reviews', ReviewController::class)->names('user.reviews')->except(['create', 'store', 'show']);
    //user courses
   Route::get('/my-courses', [MyCourseController::class, 'myCourses'])->name('user.my.courses');
  
    Route::post('/mycourses/{courseId}/mark-lecture-completed', [MyCourseController::class, 'markLectureCompleted'])->name('course.markLectureCompleted');
    Route::post('/course/rate/{course}', [MyCourseController::class, 'submitRating'])->name('course.rate');
    // Quiz Routes
   Route::get('/mycourses/learn/{courseId}/{slug}', [MyCourseController::class, 'startLearning'])->name('course.start');
    Route::post('/mycourses/{courseId}/quiz/{quizId}/submit', [MyCourseController::class, 'submitQuiz'])->name('course.quiz.submit');
    //certificate routes
    Route::get('/mycourses/certificate/{courseId}', [MyCourseController::class, 'downloadCertificate'])->name('course.certificate');
    //notes routes 
   Route::resource('mycourses/notes', NotesController::class)->only(['index', 'store', 'update', 'destroy'])->names('mycourses.notes');

    //User Quiz routes
    Route::get('/quizzes', [QuizzesController::class, 'index'])->name('quizzes.index');

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
        // Courses  Routes
        Route::post('/courses/update-status', [AdminCourseController::class, 'UpdateCourseStatus'])->name('update.course.status');
        // Instructor Routes
        Route::post('/instructors/status', [InstructorManagementController::class, 'updateStatus'])->name('update.instructor.status');
        // Review Routes
        Route::get('/pending/review', [AdminReviewController::class, 'pending'])->name('pending.review');
        Route::get('/active/review', [AdminReviewController::class, 'active'])->name('active.review');
        Route::post('/update/review/status', [AdminReviewController::class, 'updateStatus'])->name('update.review.status');

        Route::get('/orders', [AllOrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AllOrdersController::class, 'show'])->name('orders.show');
    });
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    require base_path('routes/auth/instructor.php');

    Route::get('/dashboard', function () {
        return view('instructor.index');
    })->middleware(['auth:instructor'])->name('dashboard');
    Route::middleware(['auth:instructor', 'verified'])->group(function () {
      //add course routes
        Route::resource('courses', CourseController::class)->names('courses');
        Route::get('/courses/subcategory/ajax/{category_id}', [CourseController::class, 'getSubCategory'])->name('subcategory.ajax');
        Route::resource('courses.sections', CourseSectionController::class)->names('course_sections');
        Route::resource('courses.lectures', CourseLectureController::class)->names('course_lectures');
        //review routes
        Route::get('/all/review', [InstructorReviewController::class, 'index'])->name('all.review');
        // Coupon Resource Route
        Route::resource('coupon', CouponController::class)->names('coupon');
        // Quiz Routes
       Route::resource('quiz', QuizController::class)->names('quiz');
        //orders
       Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
       Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
       //notif routes 
     Route::post('/instructor/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
  
    });
});

// Frontend Routes (Public)
Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails'])->name('course.details');
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse'])->name('category.course');
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse'])->name('subcategory.course');
Route::get('/instructor/details/{id}', [IndexController::class, 'InstructorDetails'])->name('instructor.details');
Route::get('/courses', [IndexController::class, 'AllCourses'])->name('courses.all');

Route::get('/courses', [IndexController::class, 'courses'])->name('course.list');

