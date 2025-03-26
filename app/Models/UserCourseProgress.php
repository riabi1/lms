<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
    protected $fillable = ['user_id', 'course_id', 'lecture_id', 'completed', 'completed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecture()
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id'); // Changement ici
    }
}