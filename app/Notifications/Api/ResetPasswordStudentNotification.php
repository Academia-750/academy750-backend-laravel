<?php

namespace App\Notifications\Api;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordStudentNotification extends Notification implements ShouldQueue
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

    public function toMail($notifiable)
    {
        $namePerson = $notifiable->first_name;
        $dni = $notifiable->dni;
        $password_generated = $this->data["password_generated"];

        return (new MailMessage)
            ->subject("Academia 750 - Nueva Clave de acceso")
            ->greeting("<span class='greeting-text-default-mailable typography-greeting-text text-size-18'>Hola! {$namePerson}</span>")
            ->line("Tus nuevos datos de acceso son:")
            ->line("<b>Usuario</b>: {$dni}")
            /*->line("<b>Contraseña</b>: {$password_generated}")*/
            ->line("<b>Contraseña</b>: <span id='password' style='display: none;'>{$password_generated}</span>")
            ->line('<button id="show-password">Mostrar contraseña</button>')
            ->salutation("Atentamente:");
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
