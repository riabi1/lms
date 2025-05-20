<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['course_question_id', 'instructor_id', 'answer_text'];

    public function question()
    {
        return $this->belongsTo(CourseQuestion::class, 'course_question_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}