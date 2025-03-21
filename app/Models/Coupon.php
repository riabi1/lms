<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * Utiliser $guarded = [] signifie que tous les champs sont assignables, mais il est plus sûr de spécifier explicitement les champs.
     */
    protected $fillable = [
        'coupon_name',
        'coupon_discount',
        'coupon_validity',
        'course_id',
        'instructor_id', // Ajouté pour correspondre à ton contrôleur
        'created_at',
        'updated_at',
    ];

    /**
     * Relation avec le modèle Course.
     * Un coupon appartient à un cours.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
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