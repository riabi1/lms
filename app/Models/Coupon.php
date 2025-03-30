<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'coupon_name',
        'coupon_discount',
        'coupon_validity',
        'course_id',
        'instructor_id', 
        'created_at',
        'updated_at',
    ];

    /**
     * Relation avec le modèle Course.
     * Un coupon appartient à un cours.
     */
  public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Relation avec le modèle User (instructeur).
     * Un coupon appartient à un instructeur.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id', 'id');
    }
}