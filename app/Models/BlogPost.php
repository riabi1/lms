<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = ['instructor_id', 'blog_category_id', 'title', 'slug', 'content', 'image', 'status'];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

 
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}