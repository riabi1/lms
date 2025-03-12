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
}
