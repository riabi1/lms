<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'instructor_id', 'title', 'slug', 'content', 
        'image', 'status', 'approved_at'
    ];

    protected $dates = ['approved_at']; // Treat approved_at as a Carbon date

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

  
}