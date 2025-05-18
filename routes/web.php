<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\NotesController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\QuizzesController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\User\MyCourseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EarningsController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Admin\AllOrdersController;
use App\Http\Controllers\Admin\BlogPostsController;
use App\Http\Controllers\Instructor\BlogController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Admin\CouponViewController;
use App\Http\Controllers\Frontend\InvoiceController;
use App\Http\Controllers\Instructor\OrderController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\ExcelReportController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Frontend\BlogShowController;
use App\Http\Controllers\Instructor\CouponController;
use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Admin\BlogCategoriesController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\Frontend\BlogArticleController;
use App\Http\Controllers\Frontend\PaypalPaymentController;
use App\Http\Controllers\Frontend\StripePaymentController;
use App\Http\Controllers\Instructor\NotificationController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;
use App\Http\Controllers\Instructor\CourseLectureController;
use App\Http\Controllers\Instructor\CourseSectionController;
use App\Http\Controllers\Admin\InstructorManagementController;
use App\Http\Controllers\Instructor\InstructorEarningsController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Instructor\ReportController as InstructorReportController;
use App\Http\Controllers\Instructor\ReviewController as InstructorReviewController;

// Home Page Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Routes
Route::name('')->group(function () {
    require base_path('routes/auth/web.php');

  Route::get('/dashboard', [UserDashboardController::class, 'index'])->middleware('verified')->name('dashboard');
  // Data Endpoints for Charts
  Route::get('/user/dashboard/completion-data', [UserDashboardController::class, 'getCompletionData'])->name('user.completiondata');
  Route::get('/user/dashboard/quiz-performance', [UserDashboardController::class, 'getQuizPerformanceData'])->name('user.quizperformance');
  Route::get('/user/dashboard/enrollment-trends', [UserDashboardController::class, 'getEnrollmentTrendsData'])->name('user.enrollmenttrends');
  Route::get('/user/dashboard/wishlist-data', [UserDashboardController::class, 'getWishlistData'])->name('user.wishlistdata');

  Route::middleware('auth:web')->group(function () {
        // Cart Routes
        Route::post('/cart/sync', [CartController::class, 'syncTempCart'])->name('cart.sync');
        Route::get('/cart', [CartController::class, 'MyCart'])->name('cart');
        Route::get('/cart/dropdown', [CartController::class, 'cartDropdown'])->name('cart.dropdown');
        Route::post('cart/remove/{id}', [App\Http\Controllers\Frontend\CartController::class, 'CartRemove'])->name('cart.remove');
        Route::post('/coupon/apply', [CartController::class, 'CouponApply'])->name('coupon.apply');
        Route::get('/coupon/remove/{couponName}', [CartController::class, 'CouponRemove'])->name('coupon.remove');
        Route::get('/checkout', [CartController::class, 'checkoutCreate'])->name('checkout.create');
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

        // Previous Routes
        Route::resource('reviews', ReviewController::class)->names('user.reviews')->except(['create', 'store', 'show']);
        
        // User Courses
        Route::get('/my-courses', [MyCourseController::class, 'myCourses'])->name('user.my.courses');
        Route::post('/mycourses/{courseId}/mark-lecture-completed', [MyCourseController::class, 'markLectureCompleted'])->name('course.markLectureCompleted');
        Route::post('/course/rate/{course}', [MyCourseController::class, 'submitRating'])->name('course.rate');
        
        // Quiz Routes
        Route::get('/mycourses/learn/{courseId}/{slug}', [MyCourseController::class, 'startLearning'])->name('course.start');
        Route::post('/mycourses/{courseId}/quiz/{quizId}/submit', [MyCourseController::class, 'submitQuiz'])->name('course.quiz.submit');
        
        // Certificate Routes
        Route::get('/mycourses/certificate/{courseId}', [MyCourseController::class, 'downloadCertificate'])->name('course.certificate');
        
        // Notes Routes
        Route::get('/mycourses', [NotesController::class, 'index'])->name('mycourses.index');
        Route::post('/mycourses/{courseId}/notes', [NotesController::class, 'store'])->name('mycourses.notes.store');
        Route::put('/mycourses/notes/{id}', [NotesController::class, 'update'])->name('mycourses.notes.update');
        Route::delete('/mycourses/notes/{id}', [NotesController::class, 'destroy'])->name('mycourses.notes.destroy');
        
        // User Wishlist Routes
        Route::get('/quizzes', [QuizzesController::class, 'index'])->name('quizzes.index');
        Route::post('/wishlist/add/{course_id}', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::post('/wishlist/remove/{course_id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        
        // Blog Comment Routes
        Route::post('/blog/{slug}/comments', [BlogArticleController::class, 'storeComment'])->name('comments.store');
        Route::post('/blog/{slug}/comments/{commentId}/reply', [BlogArticleController::class, 'replyComment'])->name('comments.reply');

        // Chat Routes for User
        Route::get('/chat', [MessageController::class, 'index'])->name('chat');
        Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{conversation}/send', [MessageController::class, 'send'])->name('messages.send');
        Route::post('/messages/{conversation}/typing', [MessageController::class, 'typing'])->name('messages.typing');

        // Report Routes
        Route::get('/reports', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/create', [ReportController::class, 'create'])->name('report');
        Route::post('/report', [ReportController::class, 'store'])->name('report.submit');
        Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/feedback', [ReportController::class, 'storeFeedback'])->name('reports.feedback');
        
        Route::get('/notifications/{id}/mark-as-read', [UserNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::post('/notifications/mark-all-as-read', [UserNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/notifications/{notification}/read', [UserNotificationController::class, 'read'])->name('notifications.read');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    require base_path('routes/auth/admin.php');

    Route::get('/dashboard', function () {
        return view('admin.index');
    })->middleware(['auth:admin'])->name('dashboard');

    Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);
        Route::resource('courses', AdminCourseController::class)->names('courses');
        Route::resource('instructors', InstructorManagementController::class)->names('instructors');
        Route::resource('report-categories', ReportCategoryController::class)->names('report-categories');
        Route::post('/courses/update-status', [AdminCourseController::class, 'updateCourseStatus'])->name('courses.updateStatus');
        Route::post('/instructors/update-status', [InstructorManagementController::class, 'updateStatus'])->name('instructors.updateStatus');
        Route::get('/instructors/{id}/download-cv', [InstructorManagementController::class, 'downloadCv'])->name('instructors.downloadCv');

        Route::get('/pending/review', [AdminReviewController::class, 'pending'])->name('pending.review');
        Route::get('/active/review', [AdminReviewController::class, 'active'])->name('active.review');
        Route::post('/update/review/status', [AdminReviewController::class, 'updateStatus'])->name('update.review.status');

        Route::get('/orders', [AllOrdersController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [AllOrdersController::class, 'show'])->name('orders.show');

        Route::get('/coupons', [CouponViewController::class, 'index'])->name('coupon.index');
        Route::get('/coupons/{coupon}', [CouponViewController::class, 'show'])->name('coupon.show');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');

        Route::get('/site-settings', [SettingController::class, 'siteSetting'])->name('site.settings');
        Route::post('/site-settings/update', [SettingController::class, 'updateSite'])->name('site.update');

        // Blog Category Routes
        Route::resource('blog-categories', BlogCategoriesController::class);
        Route::get('/blog-posts', [BlogPostsController::class, 'index'])->name('blog-posts.index');
        Route::post('/blog-posts/{id}/toggle', [BlogPostsController::class, 'toggle'])->name('blog-posts.toggle');

        // Comment Routes
        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::put('/comments/{id}/toggle/{type?}', [CommentController::class, 'toggleApproval'])->name('comments.toggle');
        Route::delete('/comments/{id}/{type?}', [CommentController::class, 'destroy'])->name('comments.destroy');

        Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}', [AdminReportController::class, 'update'])->name('reports.update');

        // Excel Export Routes
        Route::get('/excel', [ExcelReportController::class, 'index'])->name('excel.index');
        Route::get('/excel/enrollments/export', [ExcelReportController::class, 'exportEnrollments'])->name('excel.enrollments.export');
        Route::get('/excel/payments/export', [ExcelReportController::class, 'exportPayments'])->name('excel.payments.export');
        Route::get('/excel/users/export', [ExcelReportController::class, 'exportUsers'])->name('excel.users.export');
        Route::get('/excel/instructors/export', [ExcelReportController::class, 'exportInstructors'])->name('excel.instructors.export');
        Route::get('/excel/orders/export', [ExcelReportController::class, 'exportOrders'])->name('excel.orders.export');
        Route::get('/excel/courses/export', [ExcelReportController::class, 'exportCourses'])->name('excel.courses.export');
        Route::get('/excel/all/export', [ExcelReportController::class, 'exportAll'])->name('excel.all.export');
    });
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    require base_path('routes/auth/instructor.php');

  Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');


  Route::middleware(['auth:instructor', 'verified'])->group(function () {
        Route::resource('courses', CourseController::class)->names('courses');
        Route::get('/courses/subcategory/ajax/{category_id}', [CourseController::class, 'getSubCategory'])->name('subcategory.ajax');
        Route::resource('courses.sections', CourseSectionController::class)->names('course_sections');
        Route::resource('courses.lectures', CourseLectureController::class)->names('course_lectures');

        Route::get('/all/review', [InstructorReviewController::class, 'index'])->name('all.review');

        Route::resource('coupon', CouponController::class)->names('coupon');
        Route::put('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupon.toggleStatus');

        Route::resource('quiz', QuizController::class)->names('quiz');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::get('/reviews/{review}', [InstructorReviewController::class, 'show'])->name('reviews.show');

        // Blog Routes 
        Route::resource('blog', BlogController::class);
        Route::post('/instructor/comments/{comment}/reply', [BlogController::class, 'replyComment'])->name('comments.reply');
        
        // Chat Routes for Instructor
        Route::get('/chat', [MessageController::class, 'index'])->name('chat');
        Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{conversation}/send', [MessageController::class, 'send'])->name('messages.send');
        Route::post('/messages/{conversation}/typing', [MessageController::class, 'typing'])->name('messages.typing');

        Route::get('/earnings', [InstructorEarningsController::class, 'index'])->name('earnings');

        // Report Routes
        Route::get('/reports', [InstructorReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [InstructorReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [InstructorReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [InstructorReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/feedback', [InstructorReportController::class, 'storeFeedback'])->name('reports.feedback');

    });
});

// Frontend Routes (Public)
Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails'])->name('course.details');
Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart/items', [CartController::class, 'getCartItems'])->name('cart.items');
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse'])->name('category.course');
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse'])->name('subcategory.course');
Route::get('/instructor/details/{id}', [IndexController::class, 'InstructorDetails'])->name('instructor.details');
Route::get('/Allcourses', [IndexController::class, 'AllCourses'])->name('courses.all');
Route::get('/courses', [IndexController::class, 'courses'])->name('course.list');

// Invoice Routes
Route::get('/checkout/success', [InvoiceController::class, 'success'])->name('checkout.success');
Route::get('/invoice/{invoice}/download', [InvoiceController::class, 'download'])->name('invoice.download');

Route::get('/blog', [BlogArticleController::class, 'index'])->name('blog.list');
Route::get('/blog/{slug}', [BlogArticleController::class, 'show'])->name('blog.detail');

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::get('/search', [SearchController::class, 'search'])->name('search');


