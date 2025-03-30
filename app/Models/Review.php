<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Champs remplissables correspondant à la table `reviews`
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

    // Conversion du champ status en booléen
    protected $casts = [
        'status' => 'boolean',
    ];

    // Relation polymorphique avec l'entité évaluée (ex. Course)
    public function reviewable()
    {
        return $this->morphTo();
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthode pour récupérer le cours (si reviewable est un Course)
    public function course()
    {
        return $this->reviewable_type === 'App\Models\Course' 
            ? $this->reviewable 
            : null;
    }

    // Méthode pour récupérer l'instructeur indirectement via le cours
    public function instructor()
    {
        if ($this->reviewable_type === 'App\Models\Course' && $this->reviewable) {
            $course = $this->reviewable;
            return $course->courseable_type === 'App\Models\Instructor' && $course->courseable_id
                ? Instructor::find($course->courseable_id)
                : null;
        }
        return null;
    }
}