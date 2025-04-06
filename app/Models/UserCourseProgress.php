<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
  protected $fillable = [
        'trackable_type', 'trackable_id', 'course_id', 'lecture_id', 'completed', 'completed_at'
    ];


    /**
     * Relation polymorphique avec le modèle parent (User)
     */
    public function trackable()
    {
        return $this->morphTo();
    }
    
  

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecture()
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id'); 
    }

    public function section()
    {
        return $this->hasOneThrough(
            CourseSection::class,    // Modèle cible (final)
            CourseLecture::class,    // Modèle intermédiaire
            'id',                    // Clé primaire sur la table intermédiaire (course_lectures)
            'id',                    // Clé primaire sur la table cible (course_sections)
            'lecture_id',            // Clé étrangère sur la table courante (user_course_progress)
            'section_id'             // Clé étrangère sur la table intermédiaire (course_lectures)
        );
    }
}