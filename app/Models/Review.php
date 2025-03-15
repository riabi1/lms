<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'user_id', 'instructor_id', 'comment', 'rating', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}