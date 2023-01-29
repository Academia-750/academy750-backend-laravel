<?php

namespace App\Notifications\Api;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ContactUsHomeNotification extends Notification implements ShouldQueue
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
        $reasonMail = $this->data["reason"];
        $namePerson = $this->data["firstName"];
        $lastNamePerson = $this->data["lastName"];
        $email = $this->data["email"];
        $number_phone = $this->data["phone"];
        $comment_text = $this->data["message"];

        return (new MailMessage)
            ->subject("Contáctanos - {$namePerson} {$lastNamePerson}")
            ->greeting("<p class='center-text text-size-20 typography-greeting-text'>¡Solicitud de contacto!</p>")
            ->line("<p class='center-text text-size-17'>Motivo: <span class='typography-greeting-text'>{$reasonMail}</span></p>")
            ->line("<b>Nombre</b>: {$namePerson}")
            ->line("<b>Apellidos</b>: {$lastNamePerson}")
            ->line("<b>Email</b>: {$email}")
            ->line("<b>Teléfono</b>: {$number_phone}")
            ->line("<b>Comentario</b>: {$comment_text}")
            ->salutation("Firma");
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
