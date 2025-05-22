<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLecture extends Model
{
    use HasFactory;
    protected $fillable = [
      'course_id',
      'section_id',
      'lecture_title',
      'video',
      'content',
      'file_path',
      'external_link',
      'resources_description',
  ];

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