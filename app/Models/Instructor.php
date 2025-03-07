<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
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

    // Personnaliser la notification de vérification
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
}