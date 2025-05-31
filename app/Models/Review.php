<?php

namespace App\Models;

use App\Events\ReviewUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'user_id',
        'comment',
        'rating',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::created(function ($review) {
            ReviewUpdated::dispatch($review);
        });

        static::updated(function ($review) {
            ReviewUpdated::dispatch($review);
        });
    }

    // Relation polymorphique avec l'entité évaluée (Course)
    public function reviewable()
    {
        return $this->morphTo();
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor pour vérifier si la review concerne un cours
    public function getIsCourseAttribute()
    {
        return $this->reviewable_type === 'App\Models\Course';
    }

    // Accessor pour récupérer le cours (non comme une relation)
    public function getCourseAttribute()
    {
        return $this->is_course ? $this->reviewable : null;
    }

    // Accessor pour récupérer l'instructeur via le cours
    public function getInstructorAttribute()
    {
        if ($this->is_course && $this->reviewable) {
            $course = $this->reviewable;
            return $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
                ? Instructor::find($course->courseable_id)
                : null;
        }
        return null;
    }
}