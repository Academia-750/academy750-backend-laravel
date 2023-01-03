<?php

namespace App\Notifications\Api;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCredentialsUserNotification extends Notification/* implements ShouldQueue*/
{
    use Queueable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $dni = $notifiable->dni;
        $password_generated = $this->data["password_generated"];
        return (new MailMessage)
            ->subject("Academia 750 - Clave de acceso")
            ->greeting("<span class='greeting-text-default-mailable typography-greeting-text text-size-18'>Hola! Bienvenid@ a Academia 750!</span>")
            ->line("Te enviamos tus nuevas claves de acceso para que puedas disfrutar y aprender de todo lo que te espera con nosotros.")
            ->line("<b>Usuario</b>: {$dni}")
            ->line("<b>Contraseña</b>: {$password_generated}")
            ->salutation("¡A por todas!");
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
