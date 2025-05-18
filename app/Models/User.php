<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name', 'email','photo','phone','address','preference','grade','password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new class ($this) extends VerifyEmail {
            public function toMail($notifiable)
            {
                $verificationUrl = $this->verificationUrl($notifiable);

                return (new MailMessage)
                    ->subject('Vérifiez votre adresse email')
                    ->line('Cliquez sur le bouton ci-dessous pour vérifier votre adresse email.')
                    ->action('Vérifier l’email', $verificationUrl)
                    ->line('Si vous n’avez pas créé de compte, ignorez cet email.');
            }

            protected function verificationUrl($notifiable)
            {
                return \URL::signedRoute(
                    'verification.verify',
                    ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
                );
            }
        });
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new class($token) extends ResetPasswordNotification {
            public function toMail($notifiable)
            {
                $url = url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));

                return (new MailMessage)
                    ->subject('Reset Your Password')
                    ->line('You are receiving this email because we received a password reset request for your account.')
                    ->action('Reset Password', $url)
                    ->line('If you did not request a password reset, no further action is required.');
            }
        });
    }
    public function reviews()
{
    return $this->hasMany(Review::class, 'user_id');
}

public function questions()
    {
        return $this->hasMany(Question::class);
    }

public function orders()
    {
        return $this->hasMany(Order::class); // Assurez-vous que la classe Order existe
    }





public function reports()
{
    return $this->morphMany(Report::class, 'reporter');
}

public function conversations()
    {
        return $this->hasMany(Conversation::class, 'user_id');
    }

public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

/**
     * Relation polymorphique avec user_course_progress
     */
  public function courseProgress()
    {
        return $this->morphMany(UserCourseProgress::class, 'trackable');
    }
    /**
     * Relation polymorphique avec wishlists
     */
    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'trackable');
    }

    public function courses()
    {
        return $this->morphMany(Course::class, 'courseable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function commentReplies()
    {
        return $this->hasMany(CommentReply::class);
    }
}