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

    /**
     * Define the relationship with the Course model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * Define the relationship with the CourseSection model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
   public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
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