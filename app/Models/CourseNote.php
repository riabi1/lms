<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseNote extends Model
{
    protected $fillable = ['user_id', 'course_id', 'title', 'content', 'due_date', 'favorite', 'color','tags','screenshot'];

    protected $casts = [
        'favorite' => 'boolean',
        'due_date' => 'date',
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