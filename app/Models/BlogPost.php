<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'blog_category_id',
        'title',
        'slug',
        'content',
        'image',
        'status',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Relation avec l'instructeur (many-to-one)
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Relation avec la catégorie de blog (many-to-one)
     */
    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}