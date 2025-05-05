<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'blog_post_id', 'message', 'approved'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    public function replies()
    {
        return $this->hasMany(CommentReply::class);
    }
}