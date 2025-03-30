<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  use HasFactory;

  protected $fillable = ['category_name', 'category_slug', 'image']; // Use fillable instead of guarded for safety

  public function subcategories()
  {
    return $this->hasMany(SubCategory::class);
  }

  public function courses()
    {
        return $this->hasManyThrough(
            Course::class,           // Modèle cible (final)
            SubCategory::class,      // Modèle intermédiaire
            'category_id',           // Clé étrangère sur la table intermédiaire (sub_categories)
            'subcategory_id',        // Clé étrangère sur la table cible (courses)
            'id',                    // Clé primaire sur la table courante (categories)
            'id'                     // Clé primaire sur la table intermédiaire (sub_categories)
        );
    }
}
