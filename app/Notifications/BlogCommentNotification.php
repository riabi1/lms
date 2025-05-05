<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BlogCommentNotification extends Notification
{
    use Queueable;

    protected $blogPost;
    protected $comment;

    public function __construct($blogPost, $comment)
    {
        $this->blogPost = $blogPost;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'comment',
            'blog_id' => $this->blogPost->id,
            'blog_title' => $this->blogPost->title,
            'comment_id' => $this->comment->id,
            'message' => "Nouveau commentaire sur votre blog '{$this->blogPost->title}' par {$this->comment->user->name}",
        ];
    }
}