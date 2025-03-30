<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
    ];

    /**
     * Relation avec les articles de blog (one-to-many)
     */
    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'blog_category_id');
    }
}