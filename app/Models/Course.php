<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subcategory_id',
        'course_image',
        'course_title',
        'course_name',
        'course_name_slug',
        'description',
        'video',
        'label',
        'duration',
        'resources',
        'certificate',
        'selling_price',
        'discount_price',
        'prerequisites',
        'bestseller',
        'featured',
        'highestrated',
        'status',
        'courseable_type',
        'courseable_id',
        'created_at',
    ];

    /**
     * Get the owning courseable model (e.g., Instructor or Admin).
     */
    public function courseable()
    {
        return $this->morphTo();
    }

    /**
     * Get the instructor for the course (if courseable_type is Instructor).
     */
    public function instructor()
    {
        return $this->courseable()->where('courseable_type', Instructor::class);
    }

    /**
     * Get the category through the subcategory.
     */
    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id',           // SubCategory.id
            'id',           // Category.id
            'subcategory_id', // Course.subcategory_id
            'category_id'   // SubCategory.category_id
        );
    }

    /**
     * Get the subcategory for the course.
     */
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    /**
     * Get the reviews for the course.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get the goals for the course.
     */
    public function goals()
    {
        return $this->morphMany(CourseGoal::class, 'goalable');
    }

    /**
     * Get the sections for the course.
     */
    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    /**
     * Get the lectures for the course.
     */
    public function lectures()
    {
        return $this->hasMany(CourseLecture::class);
    }

    /**
     * Get the notes for the course (for the authenticated user).
     */
    public function notes()
    {
        return $this->hasMany(CourseNote::class)->where('user_id', auth()->id());
    }

    /**
     * Get the wishlists for the course.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the quizzes for the course.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the quiz attempts for the course through quizzes.
     */
    public function quizAttempts()
    {
        return $this->hasManyThrough(
            QuizAttempt::class,    // Target model
            Quiz::class,           // Intermediate model
            'course_id',           // Foreign key on quizzes table
            'quiz_id',             // Foreign key on quiz_attempts table
            'id',                  // Local key on courses table
            'id'                   // Local key on quizzes table
        );
    }

    /**
     * Get the questions for the course.
     */
    public function questions()
    {
        return $this->hasMany(CourseQuestion::class);
    }

    /**
     * Get the users enrolled in the course (via paid orders).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'orders', 'course_id', 'user_id')
                    ->where('payment_status', 'paid');
    }
    public function coupons()
    {
        return $this->morphMany(Coupon::class, 'couponable');
    }

    public function orders()
{
    return $this->hasMany(Order::class, 'course_id');
}
}