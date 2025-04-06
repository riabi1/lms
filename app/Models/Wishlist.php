<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = ['trackable_type', 'trackable_id', 'course_id'];

    /**
     * Get the parent trackable model (e.g., User).
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    /**
     * Get the course associated with the wishlist.
     */
   public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}