<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\CourseController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\InstructorManagementController;
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
    Route::post('/store/review', [ReviewController::class, 'StoreReview'])->name('store.review');
});
});

//user routes
Route::middleware(['auth:web'])->group(function () {
  Route::get('/my/reviews', [ReviewController::class, 'UserReviews'])->name('user.reviews');
  Route::get('/review/edit/{id}', [ReviewController::class, 'UserReviewEdit'])->name('user.review.edit');
  Route::put('/review/update/{id}', [ReviewController::class, 'UserReviewUpdate'])->name('user.review.update');
  Route::delete('/review/delete/{id}', [ReviewController::class, 'UserReviewDelete'])->name('user.review.delete');


});


// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
  require base_path('routes/auth/admin.php');
  Route::get('/dashboard', fn() => view('admin.index'))
    ->middleware(['auth:admin', 'verified'])
    ->name('dashboard');
  Route::middleware(['auth:admin', 'verified'])->group(function () {
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('profile.updatePassword');
  });


});
// Instructor Routes
Route::prefix('instructor')->name('instructor.')->group(function () {
  // Include authentication routes
  require base_path('routes/auth/instructor.php');

  // Dashboard
  Route::get('/dashboard', function () {
    return view('instructor.index');
  })->middleware(['auth:instructor', 'verified'])->name('dashboard');

  // Profile
  Route::middleware(['auth:instructor', 'verified'])->group(function () {
    Route::get('/profile/edit', [InstructorProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [InstructorProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [InstructorProfileController::class, 'updatePassword'])->name('profile.updatePassword');
  });
});





















Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'verified'])->group(function () {
  // Category Resource Routes
  Route::resource('categories', CategoryController::class)->except(['show']); // Pas de "show" si inutile

  // SubCategory Resource Routes
  Route::resource('subcategories', SubCategoryController::class)->except(['show']); // Pas de "show" si inutile


    
  // update instructor status
  Route::get('/instructors', [InstructorManagementController::class, 'index'])
    ->name('instructors.index');
  Route::post('/instructors/status', [InstructorManagementController::class, 'updateStatus'])
    ->name('update.instructor.status');

  // All Course Routes in one place
  Route::controller(AdminCourseController::class)->group(function () {
    Route::get('/all/course', 'AdminAllCourse')->name('all.course');
    Route::post('/update/course/status', 'UpdateCourseStatus')->name('update.course.status');
    Route::get('/course/details/{id}', 'AdminCourseDetails')->name('course.details');
  });
    Route::get('/pending/review', [ReviewController::class, 'AdminPendingReview'])->name('pending.review');
    Route::get('/active/review', [ReviewController::class, 'AdminActiveReview'])->name('active.review');
    Route::post('/update/review/status', [ReviewController::class, 'UpdateReviewStatus'])->name('update.review.status');
});



















// Instructor Course Routes
Route::prefix('instructor')->name('instructor.')->middleware(['auth:instructor', 'verified'])->group(function () {
  Route::controller(CourseController::class)->group(function () {
    Route::get('/all/course', 'AllCourse')->name('all.course');
    Route::get('/add/course', 'AddCourse')->name('add.course');
    Route::post('/store/course', 'StoreCourse')->name('store.course');
    Route::get('/edit/course/{id}', 'EditCourse')->name('edit.course');
    Route::post('/update/course', 'UpdateCourse')->name('update.course');
    Route::post('/update/course/image', 'UpdateCourseImage')->name('update.course.image');
    Route::post('/update/course/video', 'UpdateCourseVideo')->name('update.course.video');
    Route::post('/update/course/goal', 'UpdateCourseGoal')->name('update.course.goal');
    Route::get('/delete/course/{id}', 'DeleteCourse')->name('delete.course');
    Route::get('/subcategory/ajax/{category_id}', 'getSubCategory')->name('subcategory.ajax');
  });
  // Course Section and Lecture Routes
  Route::controller(CourseController::class)->group(function () {
    Route::get('/add/course/lecture/{id}', 'AddCourseLecture')->name('add.course.lecture');
    Route::post('/add/course/section/', 'AddCourseSection')->name('add.course.section');
    Route::post('/save-lecture/', 'SaveLecture')->name('save.lecture');
    Route::get('/edit/lecture/{id}', 'EditLecture')->name('edit.lecture');
    Route::post('/update/course/lecture', 'UpdateCourseLecture')->name('update.course.lecture');
    Route::get('/delete/lecture/{id}', 'DeleteLecture')->name('delete.lecture');
    Route::post('/delete/section/{id}', 'DeleteSection')->name('delete.section');
  });
  Route::get('/all/review', [ReviewController::class, 'InstructorAllReview'])->name('all.review');
});


Route::get('/course/details/{id}/{slug}', [IndexController::class, 'CourseDetails']);
Route::get('/category/{id}/{slug}', [IndexController::class, 'CategoryCourse']);
Route::get('/subcategory/{id}/{slug}', [IndexController::class, 'SubCategoryCourse']);