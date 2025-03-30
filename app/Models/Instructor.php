<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class Instructor extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $guard = 'instructor';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'instructors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
   protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'photo',
        'bio',           
        'experience',    
        'skills',        
        'education',     
        'website',       
        'location',      
        'email_verified_at',
        'password',
        'provider_name',
        'provider_id',
        'status',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean', // Assuming status is 0 or 1
    ];

    /**
     * Define the relationship with the Course model.
     */
   public function courses()
    {
        return $this->morphMany(Course::class, 'courseable');
    }
    /**
     * Send the email verification notification.
     *
     * @return void
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
                    'instructor.verification.verify',
                    ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
                );
            }
        });
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new class($token) extends ResetPasswordNotification {
            public function toMail($notifiable)
            {
                $url = url(route('instructor.password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));

                return (new MailMessage)
                    ->subject('Reset Your Instructor Password')
                    ->line('You are receiving this email because we received a password reset request for your instructor account.')
                    ->action('Reset Password', $url)
                    ->line('If you did not request a password reset, no further action is required.');
            }
        });
    }

    public function reviews()
    {
    return $this->hasMany(Review::class, 'instructor_id');
    }

 public function reports()
{
    return $this->morphMany(Report::class, 'reporter');
}
public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }
public function conversations()
    {
        return $this->hasMany(Conversation::class, 'instructor_id');
    }

public function sentMessages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

}