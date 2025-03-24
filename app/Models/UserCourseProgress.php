<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
    protected $fillable = ['user_id', 'course_id', 'progress', 'completed_lectures'];

    protected $casts = [
        'completed_lectures' => 'array', // Convertir JSON en tableau PHP
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}