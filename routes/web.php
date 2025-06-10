<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\User\ChatController;
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
use App\Http\Controllers\Instructor\QuestionController;
use App\Http\Controllers\Admin\AdmindashboardController;
use App\Http\Controllers\Admin\BlogCategoriesController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\Frontend\BlogArticleController;
use App\Http\Controllers\User\PurchaseHistoryController;
use App\Http\Controllers\Frontend\PaypalPaymentController;
use App\Http\Controllers\Frontend\StripePaymentController;
use App\Http\Controllers\Instructor\NotificationController;
use App\Http\Controllers\Instructor\CourseLectureController;
use App\Http\Controllers\Instructor\CourseSectionController;
use App\Http\Controllers\Instructor\InstructorChatController;
use App\Http\Controllers\Admin\InstructorManagementController;
use App\Http\Controllers\Instructor\InstructorEarningsController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Instructor\ReportController as InstructorReportController;
use App\Http\Controllers\Instructor\ReviewController as InstructorReviewController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;
use App\Http\Controllers\Admin\RoleController;

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
 
    Route::get('/user/enrollmenttrends', [UserDashboardController::class, 'getEnrollmentTrendsData'])->name('enrollmenttrends');
    Route::get('/completiondata', [UserDashboardController::class, 'getCompletionData'])->name('completiondata');
Route::get('/user/quizperformance', [UserDashboardController::class, 'getQuizPerformanceData'])->name('quizperformance');
Route::get('/user/wishlistdata', [UserDashboardController::class, 'getWishlistData'])->name('wishlistdata');
Route::get('/user/categoryengagement', [UserDashboardController::class, 'getCategoryEngagementData'])->name('categoryengagement');
    
    
Route::post('/chat', [ChatController::class, 'handleChat'])->name('user.chat');
    
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
        Route::get('/mycourses/favorite-notes', [NotesController::class, 'favorites'])->name('mycourses.favorites');
        Route::post('/mycourses/notes/store/{courseId}', [NotesController::class, 'store'])->name('mycourses.notes.store');
        Route::put('/mycourses/notes/update/{id}', [NotesController::class, 'update'])->name('mycourses.notes.update');
        Route::post('/mycourses/notes/toggle-favorite/{id}', [NotesController::class, 'toggleFavorite'])->name('mycourses.notes.toggle-favorite');
        Route::delete('/mycourses/notes/destroy/{id}', [NotesController::class, 'destroy'])->name('mycourses.notes.destroy');
        Route::post('/course/{courseId}/question/submit', [MyCourseController::class, 'submitQuestion'])->name('course.question.submit');
        Route::put('{courseId}/question/update', [MyCourseController::class, 'updateQuestion'])->name('course.question.update');
        Route::delete('{courseId}/question/destroy', [MyCourseController::class, 'destroyQuestion'])->name('course.question.destroy');
        Route::get('/purchase-history', [PurchaseHistoryController::class, 'index'])->name('purchase.history');


        // User Wishlist Routes
        Route::get('/quizzes', [QuizzesController::class, 'index'])->name('quizzes.index');
        Route::post('/wishlist/add/{course_id}', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::post('/wishlist/remove/{course_id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        
        // Blog Comment Routes
        Route::post('/blog/{slug}/comments', [BlogArticleController::class, 'storeComment'])->name('comments.store');
        Route::post('/blog/{slug}/comments/{commentId}/reply', [BlogArticleController::class, 'replyComment'])->name('comments.reply');

        // Chat Routes for User
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{conversation}/send', [MessageController::class, 'send'])->name('messages.send');
  
    

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

  Route::get('/dashboard', [AdmindashboardController::class, 'index'])
      ->middleware(['auth:admin'])->name('dashboard');

  Route::middleware(['auth:admin', 'verified'])->group(function () {
      // Role Management
      Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:admin.roles.index')->name('roles.index');
      Route::post('/roles/{admin}/assign', [RoleController::class, 'assignRole'])->middleware('permission:admin.roles.assign')->name('roles.assign');
      Route::post('/roles/create', [RoleController::class, 'createRole'])->middleware('permission:admin.roles.create')->name('roles.create');
      Route::post('/roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->middleware('permission:admin.permissions.manage')->name('roles.permissions');

      // Category Management
      Route::resource('categories', CategoryController::class)->except(['show'])->middleware([
          'index' => 'permission:admin.categories.index',
          'create' => 'permission:admin.categories.create',
          'store' => 'permission:admin.categories.store',
          'edit' => 'permission:admin.categories.edit',
          'update' => 'permission:admin.categories.update',
          'destroy' => 'permission:admin.categories.destroy',
      ]);

      // Subcategory Management
      Route::resource('subcategories', SubCategoryController::class)->except(['show'])->middleware([
          'index' => 'permission:admin.subcategories.index',
          'create' => 'permission:admin.subcategories.create',
          'store' => 'permission:admin.subcategories.store',
          'edit' => 'permission:admin.subcategories.edit',
          'update' => 'permission:admin.subcategories.update',
          'destroy' => 'permission:admin.subcategories.destroy',
      ]);

      // Course Management
      Route::get('/courses', [AdminCourseController::class, 'index'])->middleware('permission:admin.courses.index')->name('courses.index');
      Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->middleware('permission:admin.courses.show')->name('courses.show');
      Route::post('/courses/update-status', [AdminCourseController::class, 'updateCourseStatus'])->middleware('permission:admin.courses.updateStatus')->name('courses.updateStatus');

      // Instructor Management
      Route::get('/instructors', [InstructorManagementController::class, 'index'])->middleware('permission:admin.instructors.index')->name('instructors.index');
      Route::post('/instructors/update-status', [InstructorManagementController::class, 'updateStatus'])->middleware('permission:admin.instructors.updateStatus')->name('instructors.updateStatus');
      Route::get('/instructors/{id}/download-cv', [InstructorManagementController::class, 'downloadCv'])->middleware('permission:admin.instructors.downloadCv')->name('instructors.downloadCv');

      // Review Management
      Route::get('/pending/review', [AdminReviewController::class, 'pending'])->middleware('permission:admin.pending.review')->name('pending.review');
      Route::get('/active/review', [AdminReviewController::class, 'active'])->middleware('permission:admin.active.review')->name('active.review');
      Route::post('/update/review/status', [AdminReviewController::class, 'updateStatus'])->middleware('permission:admin.update.review.status')->name('update.review.status');

      // Order Management
      Route::get('/orders', [AllOrdersController::class, 'index'])->middleware('permission:admin.orders.index')->name('orders.index');
      Route::get('/orders/{id}', [AllOrdersController::class, 'show'])->middleware('permission:admin.orders.show')->name('orders.show');

      // Coupon Management
      Route::get('/coupons', [CouponViewController::class, 'index'])->middleware('permission:admin.coupon.index')->name('coupon.index');
      Route::get('/coupons/{coupon}', [CouponViewController::class, 'show'])->middleware('permission:admin.coupon.show')->name('coupon.show');

      // User Management
      Route::get('/users', [UserController::class, 'index'])->middleware('permission:admin.users.index')->name('users.index');

      // Site Settings
      Route::get('/site-settings', [SettingController::class, 'siteSetting'])->middleware('permission:admin.site.settings')->name('site.settings');
      Route::post('/site-settings/update', [SettingController::class, 'updateSite'])->middleware('permission:admin.site.update')->name('site.update');

      // Blog Category Management
      Route::resource('blog-categories', BlogCategoriesController::class)->middleware([
          'index' => 'permission:admin.blog-categories.index',
          'create' => 'permission:admin.blog-categories.create',
          'store' => 'permission:admin.blog-categories.store',
          'edit' => 'permission:admin.blog-categories.edit',
          'update' => 'permission:admin.blog-categories.update',
          'destroy' => 'permission:admin.blog-categories.destroy',
      ]);

      // Blog Post Management
      Route::get('/blog-posts', [BlogPostsController::class, 'index'])->middleware('permission:admin.blog-posts.index')->name('blog-posts.index');
      Route::post('/blog-posts/{id}/toggle', [BlogPostsController::class, 'toggle'])->middleware('permission:admin.blog-posts.toggle')->name('blog-posts.toggle');

      // Comment Management
      Route::get('/comments', [CommentController::class, 'index'])->middleware('permission:admin.comments.index')->name('comments.index');
      Route::put('/comments/{id}/toggle/{type?}', [CommentController::class, 'toggleApproval'])->middleware('permission:admin.comments.toggle')->name('comments.toggle');
      Route::delete('/comments/{id}/{type?}', [CommentController::class, 'destroy'])->middleware('permission:admin.comments.destroy')->name('comments.destroy');

      // Earnings
      Route::get('/earnings', [EarningsController::class, 'index'])->middleware('permission:admin.earnings')->name('earnings');

      // Report Management
      Route::get('/reports', [AdminReportController::class, 'index'])->middleware('permission:admin.reports.index')->name('reports.index');
      Route::patch('/reports/{report}', [AdminReportController::class, 'update'])->middleware('permission:admin.reports.update')->name('reports.update');

      // Report Category Management
      Route::resource('report-categories', ReportCategoryController::class)->names('report-categories')->middleware([
          'index' => 'permission:admin.report-categories.index',
          'create' => 'permission:admin.report-categories.create',
          'store' => 'permission:admin.report-categories.store',
          'edit' => 'permission:admin.report-categories.edit',
          'update' => 'permission:admin.report-categories.update',
          'destroy' => 'permission:admin.report-categories.destroy',
      ]);

      // Excel Export Routes
      Route::get('/admin/excel', [ExcelReportController::class, 'index'])->middleware('permission:admin.excel.index')->name('excel.index');
      Route::get('/admin/excel/export/enrollments', [ExcelReportController::class, 'exportEnrollments'])->middleware('permission:admin.excel.enrollments')->name('excel.enrollments');
      Route::get('/admin/excel/export/payments', [ExcelReportController::class, 'exportPayments'])->middleware('permission:admin.excel.payments')->name('excel.payments');
      Route::get('/admin/excel/export/users', [ExcelReportController::class, 'exportUsers'])->middleware('permission:admin.excel.users')->name('excel.users');
      Route::get('/admin/excel/export/instructors', [ExcelReportController::class, 'exportInstructors'])->middleware('permission:admin.excel.instructors')->name('excel.instructors');
      Route::get('/admin/excel/export/orders', [ExcelReportController::class, 'exportOrders'])->middleware('permission:admin.excel.orders')->name('excel.orders');
      Route::get('/admin/excel/export/courses', [ExcelReportController::class, 'exportCourses'])->middleware('permission:admin.excel.courses')->name('excel.courses');
      Route::get('/admin/excel/export/admins', [ExcelReportController::class, 'exportAdmins'])->middleware('permission:admin.excel.admins')->name('excel.admins');
      Route::get('/admin/excel/export/blog-posts', [ExcelReportController::class, 'exportBlogPosts'])->middleware('permission:admin.excel.blog_posts')->name('excel.blog_posts');
      Route::get('/admin/excel/export/blog-categories', [ExcelReportController::class, 'exportBlogCategories'])->middleware('permission:admin.excel.blog_categories')->name('excel.blog_categories');
      Route::get('/admin/excel/export/coupons', [ExcelReportController::class, 'exportCoupons'])->middleware('permission:admin.excel.coupons')->name('excel.coupons');
  });
});

// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
    require base_path('routes/auth/instructor.php');

  Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
 
  Route::middleware(['auth:instructor', 'verified'])->group(function () {
    Route::post('/instructor/chat', [InstructorChatController::class, 'handleChat'])->name('chat.handle');
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
        Route::get('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::get('/reviews/{review}', [InstructorReviewController::class, 'show'])->name('reviews.show');

        // Blog Routes 
        Route::resource('blog', BlogController::class);
        Route::post('/instructor/comments/{comment}/reply', [BlogController::class, 'replyComment'])->name('comments.reply');
        
        // Chat Routes for Instructor
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{conversation}/send', [MessageController::class, 'send'])->name('messages.send');
    

        Route::get('/earnings', [InstructorEarningsController::class, 'index'])->name('earnings');

        // Report Routes
        Route::get('/reports', [InstructorReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create', [InstructorReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [InstructorReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}', [InstructorReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/feedback', [InstructorReportController::class, 'storeFeedback'])->name('reports.feedback');
        //question routes
        Route::get('/questions', [QuestionController::class, 'index'])->name('question.index');
        Route::post('/question/answer/store', [QuestionController::class, 'storeAnswer'])->name('question.answer.store');
        Route::put('/question/answer/update', [QuestionController::class, 'updateAnswer'])->name('question.answer.update');
        Route::delete('/question/answer/destroy', [QuestionController::class, 'destroyAnswer'])->name('question.answer.destroy');
        
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


