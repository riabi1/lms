<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'instructor_id', 'subject', 'question', 'answer_text', 'status',
    ];

   public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
  
  public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}