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
     * The attributes that are not mass assignable.
     *
     * @var array
     */
  protected $fillable = [
        'category_id',
        'subcategory_id',
        'instructor_id',
        'course_image',
        'course_title',
        'course_name',
        'course_name_slug',
        'description',
        'video',
        'label',
        'duration',
        'certificate',
        'selling_price',
        'discount_price',
        'prerequisites',
        'bestseller',
        'featured',
        'highestrated',
        'status',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id'); // Spécifie la clé étrangère
    }

public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

public function category()
    {
        return $this->hasOneThrough(Category::class, Subcategory::class, 'id', 'id', 'subcategory_id', 'category_id');
    }



  public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

  
public function goals()
{
    return $this->morphMany(CourseGoal::class, 'goalable');
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
            QuizAttempt::class,    // Modèle cible (final)
            Quiz::class,           // Modèle intermédiaire
            'course_id',           // Clé étrangère sur la table intermédiaire (quizzes)
            'quiz_id',             // Clé étrangère sur la table cible (quiz_attempts)
            'id',                  // Clé primaire sur la table courante (courses)
            'id'                   // Clé primaire sur la table intermédiaire (quizzes)
        );
    }

    public function courseable()
    {
        return $this->morphTo();
    }
}