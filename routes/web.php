<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\NotesController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\QuizzesController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\User\MyCourseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Admin\AllOrdersController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Admin\CouponViewController;
use App\Http\Controllers\Frontend\InvoiceController;
use App\Http\Controllers\Instructor\OrderController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\CouponController; 
use App\Http\Controllers\Frontend\PaypalPaymentController;
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
 Route::get('/cart', [CartController::class, 'MyCart'])->name('cart');
Route::post('/cart/add/{id}', [CartController::class, 'AddToCart'])->name('cart.add');
Route::delete('/cart/remove/{id}', [CartController::class, 'CartRemove'])->name('cart.remove');
Route::post('/coupon/apply', [CartController::class, 'CouponApply'])->name('coupon.apply');
Route::get('/coupon/remove/{couponName}', [CartController::class, 'CouponRemove'])->name('coupon.remove');
Route::get('/checkout', [CartController::class, 'CheckoutCreate'])->name('checkout.create');

// Stripe Payment Routes
Route::post('/pay/stripe', [StripePaymentController::class, 'payWithStripe'])->name('pay.stripe');

// PayPal Payment Routes
Route::get('/pay/paypal', [PaypalPaymentController::class, 'payWithPaypal'])->name('pay.paypal');
Route::get('/paypal/success', [PaypalPaymentController::class, 'paypalSuccess'])->name('paypal.success');
Route::get('/paypal/cancel', [PaypalPaymentController::class, 'paypalCancel'])->name('paypal.cancel');

// Success Route
Route::get('/order/success', function () {
    return view('User.checkout.success');
})->name('order.success');
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
 Route::get('/mycourses', [NotesController::class, 'index'])->name('mycourses.index');
    Route::post('/mycourses/{courseId}/notes', [NotesController::class, 'store'])->name('mycourses.notes.store');
    Route::put('/mycourses/notes/{id}', [NotesController::class, 'update'])->name('mycourses.notes.update');
    Route::delete('/mycourses/notes/{id}', [NotesController::class, 'destroy'])->name('mycourses.notes.destroy');
  
    //User wishlist routes
    Route::get('/quizzes', [QuizzesController::class, 'index'])->name('quizzes.index');
    Route::post('/wishlist/add/{course_id}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{course_id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Include admin authentication routes
    require base_path('routes/auth/admin.php');

    // Dashboard route (requires admin authentication)
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->middleware(['auth:admin'])->name('dashboard');

    // Routes requiring authentication and verification
    Route::middleware(['auth:admin', 'verified'])->group(function () {
        // Resource Routes
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->names('courses');
        Route::resource('instructors', InstructorManagementController::class)->names('instructors');

        // Course Routes
        Route::post('/courses/update-status', [AdminCourseController::class, 'updateCourseStatus'])->name('courses.updateStatus');

        // Instructor Routes
        Route::post('/instructors/update-status', [InstructorManagementController::class, 'updateStatus'])->name('instructors.updateStatus');

        // Review Routes
        Route::get('/pending/review', [AdminReviewController::class, 'pending'])->name('pending.review');
        Route::get('/active/review', [AdminReviewController::class, 'active'])->name('active.review');
        Route::post('/update/review/status', [AdminReviewController::class, 'updateStatus'])->name('update.review.status');

        // Order Routes
        Route::get('/orders', [AllOrdersController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AllOrdersController::class, 'show'])->name('orders.show');

        // Coupon Routes
        Route::get('/coupons', [CouponViewController::class, 'index'])->name('coupon.index');
        Route::get('/coupons/{coupon}', [CouponViewController::class, 'show'])->name('coupon.show');

        // Users Route
        Route::get('/users', [UserController::class, 'index'])->name('users.index');

        // Site Settings Routes
        Route::get('/site-settings', [SettingController::class, 'siteSetting'])->name('site.settings');
        Route::post('/site-settings/update', [SettingController::class, 'updateSite'])->name('site.update');
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
        Route::put('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupon.toggleStatus');
        // Quiz Routes
       Route::resource('quiz', QuizController::class)->names('quiz');
        //orders
       Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
       Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
       //order notif
     Route::post('/instructor/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
     //review notif
     Route::get('/instructor/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    });
});

// Frontend Routes (Public)
Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails'])->name('course.details');
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse'])->name('category.course');
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse'])->name('subcategory.course');
Route::get('/instructor/details/{id}', [IndexController::class, 'InstructorDetails'])->name('instructor.details');
Route::get('/courses', [IndexController::class, 'AllCourses'])->name('courses.all');

Route::get('/courses', [IndexController::class, 'courses'])->name('course.list');

  // invoice 
 Route::get('/checkout/success', [InvoiceController::class, 'success'])->name('checkout.success');
Route::get('/invoice/{invoice}/download', [InvoiceController::class, 'download'])->name('invoice.download');

