<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class Instructor extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $guard = 'instructor';

    protected $fillable = [
        'name', 'email', 'password',
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
                    'instructor.verification.verify',
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
}