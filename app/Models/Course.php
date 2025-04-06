<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Instructor;
use App\Models\Review;
use App\Models\CourseGoal;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use App\Models\CourseNote;
use App\Models\Wishlist;
use App\Models\Quiz;
use App\Models\QuizAttempt;

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
        'resources', // Added from schema
        'certificate',
        'selling_price',
        'discount_price',
        'prerequisites',
        'bestseller',
        'featured',
        'highestrated',
        'status',
        'courseable_type', // Added for polymorphic relationship
        'courseable_id',   // Added for polymorphic relationship
        'created_at',      // Added to allow setting timestamp manually
    ];

    public function goals()
    {
        return $this->morphMany(CourseGoal::class, 'goalable');
    }

    public function courseable()
    {
        return $this->morphTo();
    }

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

public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    public function lectures()
    {
        return $this->hasMany(CourseLecture::class);
    }

    public function notes()
    {
        return $this->hasMany(CourseNote::class)->where('user_id', auth()->id());
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

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

public function questions()
    {
        return $this->hasMany(Question::class);
    }

public function instructor()
    {
        return $this->courseable;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'orders', 'course_id', 'user_id')
                    ->where('payment_status', 'paid');
    }
}