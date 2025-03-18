<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
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
     * Define the relationship with the CourseLecture model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lectures()
    {
        return $this->hasMany(CourseLecture::class, 'section_id', 'id');
    }
}