<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reporter_id', 'reporter_type', 'course_id', 'title', 
        'description', 'status', 'admin_id', 'resolution_notes'
    ];

    public function reporter()
    {
        return $this->morphTo(); // Polymorphic relationship to User or Instructor
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

  
}