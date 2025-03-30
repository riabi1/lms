<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLecture extends Model
{
    use HasFactory;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

   
   public function course()
    {
        return $this->belongsTo(Course::class);
    }

  
  public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function progress()
    {
        return $this->hasMany(UserCourseProgress::class, 'lecture_id');
    }

    public function getResourcesCountAttribute()
    {
        return collect([
            $this->additional_video,
            $this->file_path,
            $this->external_link,
        ])->filter(function ($value) {
            return !is_null($value) && $value !== '';
        })->count();
    }
}